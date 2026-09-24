# Roadmap Técnico: Resolución de Barreras Técnicas en Entorno Ngrok y Sitemap XML

## Contexto y Visión Estratégica

En el marco del proyecto **Reposa+** (E-Commerce de alto rendimiento en Laravel 11/12+ bajo PHP 8.4) y dando respuesta a los hallazgos documentados en el informe técnico [`docs/informe-entorno-ngrok.md`](../informe-entorno-ngrok.md), este roadmap define la especificación técnica formal bajo la metodología **Tríada (Scrum/Kanban + Métrica v3 + SDD)** para subsanar los problemas detectados cuando la aplicación se expone mediante túneles seguros Ngrok:

1. **Contenido Mixto (Mixed Content):** Los assets CSS y JavaScript compilados con Vite y las URLs canónicas se generan bajo esquema `http://` debido a la pérdida de la cabecera `X-Forwarded-Proto` en el balanceador y proxy inverso local (`nginx-lb` y `nginx` PHP-FPM).
2. **Barrera de Acceso Intersticial de Ngrok (`ERR_NGROK_6024`):** El firewall de Ngrok para cuentas gratuitas bloquea navegadores reales y herramientas automatizadas (Playwright, bots de scraping, validadores de catálogo) exigiendo la cabecera `ngrok-skip-browser-warning: true`.
3. **Ausencia del Estándar de Indexación (`/sitemap.xml`):** El endpoint no existía (404 Not Found), impidiendo la auditoría automatizada, SEO formal y mapeo exhaustivo de los 8 productos del catálogo para herramientas de scraping o comparativas de mercado.

---

## Metadatos del Documento

* **Fecha:** 23 de septiembre de 2026
* **Autor:** Jonathan Quispe
* **Rama de Trabajo:** `feature/informe-entorno-ngrok`
* **Metodología:** Tríada Metodológica Formal-Ágil (Capa 1: Scrumban | Capa 2: Métrica v3 EPS-UPO | Capa 3: SDD)
* **Entornos Implicados:**
  * **Desarrollo / Túnel:** `docker-compose.dev.yml` (expuesto a Ngrok)
  * **Producción Local:** `docker-compose.yml` (`nginx-lb`, `app`, `queue`, `mysql`, `redis`, `mailhog`, `minio`)
  * **Testing:** `docker-compose.e2e.yml` y suite local Pest/PHPUnit

---

## Marco Métrica v3: Trazabilidad de Requisitos

| Código Requisito | Tipo | Denominación | Descripción y Criterio Métrica v3 |
|:---|:---:|:---|:---|
| **`RNF-007`** | Seguridad / Red | Integridad TLS y Resiliencia en Proxies Inversos | El sistema debe propagar fielmente las cabeceras `X-Forwarded-Proto` y `X-Forwarded-For` a través de toda la cadena de proxies (`ngrok` → `nginx-lb` → `nginx` → `PHP-FPM`), forzando esquema `https://` en la generación de URLs y assets cuando el tráfico exterior sea TLS. |
| **`RNF-008`** | Integración / QA | Interoperabilidad de Túnel y Bypass de Intersticial | La infraestructura de desarrollo y prueba debe disponer de mecanismos (proxy de cabeceras y soporte de headers HTTP) para evitar la interrupción de agentes automatizados por el warning `ERR_NGROK_6024`. |
| **`RF-018`** | Catálogo / SEO | Indexación y Descubrimiento Estandarizado (`/sitemap.xml`) | El sistema debe exponer un endpoint público `/sitemap.xml` con `Content-Type: application/xml` conforme al esquema `sitemaps.org/schemas/sitemap/0.9`, listando la página principal, el catálogo y cada una de las fichas de producto con sus metadatos (`loc`, `lastmod`, `changefreq`, `priority`), referenciado en `robots.txt`. |
| **`CL-0007`** | Diseño Lógico | Clase Controladora `SitemapController` | Controlador encargado de consultar el modelo `Product`, ordenar el catálogo por identificador y renderizar la vista Blade de XML de mapa del sitio. |

---

## Especificación Técnica de Producción (SDD)

### 1. Neutralización de Contenido Mixto (Mixed Content)

#### 1.1 Balanceador Nginx (`docker/nginx-lb.conf`)
* **Problema:** La directiva `proxy_set_header X-Forwarded-Proto $scheme;` sobreescribe la cabecera original con `http`, dado que el balanceador escucha en el puerto 80 no cifrado.
* **Especificación:**
  * Declarar un mapeo condicional en el bloque `http`:
    ```nginx
    map $http_x_forwarded_proto $forwarded_proto {
        default $http_x_forwarded_proto;
        ""      $scheme;
    }
    ```
  * Asignar en la directiva proxy: `proxy_set_header X-Forwarded-Proto $forwarded_proto;`.
  * Mantener `proxy_set_header Host $http_host;` y `proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;`.

#### 1.2 Servidor Web de Aplicación (`docker/nginx.conf`)
* **Especificación:** Asegurar que FastCGI transmita las cabeceras `HTTP_X_FORWARDED_PROTO`, `HTTP_X_FORWARDED_FOR`, `HTTP_X_FORWARDED_HOST` y `HTTP_X_FORWARDED_PORT` a PHP-FPM:
  ```nginx
  fastcgi_param HTTP_X_FORWARDED_PROTO $http_x_forwarded_proto;
  fastcgi_param HTTP_X_FORWARDED_FOR $http_x_forwarded_for;
  fastcgi_param HTTP_X_FORWARDED_HOST $http_x_forwarded_host;
  fastcgi_param HTTP_X_FORWARDED_PORT $http_x_forwarded_port;
  ```

#### 1.3 Blindaje de Esquema en Laravel (`AppServiceProvider.php`)
* **Especificación:** En el método `boot()`, verificar si la petición entrante viaja bajo cabecera `x-forwarded-proto === 'https'` o si `config('app.url')` inicia con `https://`, forzando el esquema con `URL::forceScheme('https')`.

---

### 2. Infraestructura de Proxy para Bypass de Browser Warning de Ngrok

* **Imagen Oficial:** `igops/ngrok-skip-browser-warning:latest`
* **Entorno Primario:** `docker-compose.dev.yml` (entorno de desarrollo expuesto por túneles).
* **Entorno Opcional:** `docker-compose.yml` (bajo perfil `tunnel`).
* **Configuración del Servicio:**
  ```yaml
  ngrok-proxy:
    image: igops/ngrok-skip-browser-warning:latest
    container_name: reposaplus_ngrok_proxy
    restart: unless-stopped
    ports:
      - "8080:8080"
    environment:
      - UPSTREAM=http://app:8000
      - ADD_HEADER_ACCESS_CONTROL_ALLOW_ORIGIN=*
    depends_on:
      - app
  ```

---

### 3. Implementación del Protocolo Sitemap 0.9 (`/sitemap.xml`)

#### 3.1 Contrato del Endpoint
* **Ruta:** `GET /sitemap.xml`
* **Nombre de Ruta:** `sitemap`
* **Código de Estado:** `200 OK`
* **Cabecera HTTP:** `Content-Type: application/xml; charset=utf-8` (o `text/xml; charset=utf-8`)
* **Esquema XML:**
  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      <url>
          <loc>https://.../</loc>
          <changefreq>daily</changefreq>
          <priority>1.0</priority>
      </url>
      <url>
          <loc>https://.../catalog</loc>
          <changefreq>daily</changefreq>
          <priority>0.9</priority>
      </url>
      <!-- Por cada producto en catálogo -->
      <url>
          <loc>https://.../catalog/{id}</loc>
          <lastmod>YYYY-MM-DD</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.8</priority>
      </url>
  </urlset>
  ```

#### 3.2 Actualización de `robots.txt`
* Añadir directiva `Sitemap: /sitemap.xml` en `public/robots.txt`.

---

## Criterios de Aceptación Cuantificables (Executable Specs) - Estado de Certificación

1. **AC-1 (Sitemap HTTP Status & Content-Type):** [SUPERADO] Una petición `GET /sitemap.xml` retorna código HTTP 200 y Content-Type con `xml`.
2. **AC-2 (Sitemap XML Well-Formedness):** [SUPERADO] La carga del contenido con `simplexml_load_string()` no produce errores de sintaxis y contiene el namespace `http://www.sitemaps.org/schemas/sitemap/0.9`.
3. **AC-3 (Presencia de URLs Esenciales):** [SUPERADO] El sitemap contiene la raíz (`/`), el catálogo (`/catalog`) y exactamente las URLs de los productos registrados en base de datos.
4. **AC-4 (Integridad de Metadatos):** [SUPERADO] Cada entrada de producto contiene etiquetas `<loc>`, `<lastmod>` (formato de fecha válido), `<changefreq>` y `<priority>`.
5. **AC-5 (Preservación de Esquema HTTPS en Proxies):** [SUPERADO] Al simular la cabecera `X-Forwarded-Proto: https`, las URLs generadas por la aplicación (incluyendo rutas de sitemap y assets) inician indefectiblemente por `https://`.
6. **AC-6 (Quality Gate Estilístico y Funcional):** [SUPERADO] Laravel Pint no reporta ninguna desviación de estilo PSR-12 (137 archivos limpios) y la suite de tests Pest pasa al 100% (185/185 tests, 809 aserciones).

---

## Conclusiones de la Evaluación de Túneles y Transición Metodológica

1. **Evaluación de Exposición Externa ante Agentes IA (Google Gemini):**
   - Se evaluaron túneles Ngrok, Cloudflare Quick Tunnels (`trycloudflare.com`) y Localhost.run (`lhr.life`).
   - Se demostró que los agentes conversacionales externos no son viables para auditar URLs efímeras vivas debido a:
     * Restricciones de crawler por cabecera `x-robots-tag: none` en Cloudflare.
     * Denylist perimetral activa contra ataques SSRF en Google Gemini (0 peticiones cursadas al servidor).
     * Incapacidad de los chatbots web para inyectar cabeceras personalizadas (`ngrok-skip-browser-warning`).
2. **Cierre de Servicios de Túnel:**
   - Todos los procesos en background de túneles (`cloudflared`, `ngrok`, `ssh localhost.run`) han sido terminados de forma limpia tras las pruebas.
3. **Definición del Siguiente Hito de QA:**
   - La auditoría técnica formal y reproducible para la memoria del TFG se efectuará en local mediante **Google Lighthouse**:
     ```bash
     npx lighthouse http://localhost:8000 --view --output=html --output-path=docs/artefactos/lighthouse-report.html
     ```
   - Este enfoque elimina dependencias de red externa, asegura determinismo en las métricas de rendimiento (Core Web Vitals) y proporciona informes auditables de Accesibilidad (WCAG 2.1), SEO y Buenas Prácticas.

