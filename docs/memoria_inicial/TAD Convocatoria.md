Autora: Olga M. Moreno Martín

# GUION Y PLANTILLA DE DEFENSA (15 MIN) y cómo enfrentar subida de nota o recuperación de TAD

## 1. Datos para rellenar (copiar/pegar)

Rellena estos campos antes de ensayar. Mantén consistencia de nombres y términos (App/Proyecto), no olvides que esta información debe estar presente siempre:

**** Nombre de la app/proyecto: ________________________________**** Subtítulo (qué hace en 1 frase): ___________________________

**** Integrantes y rol (máx. 1 línea por persona): _______________**** Ciclo / módulo / centro / año/ logo centro: _________________

**** Tecnologías (Front/Back/BD/Deploy): _________________________**** Funcionalidades clave (3–5): _______________________________

**** Diferenciación frente a alternativas: _______________________**** URL repositorio GitHub y despliegue (si existe): ____________

## 2. Cómo asegurar la máxima nota según la rúbrica

Objetivo:

Evitar fallos típicos (maquetación, exceso de texto, desorden, leer diapositivas, desviación de tiempo) y maximizar claridad, interés, y evidencias.

### 2.1 Checklist de excelencia (antes de presentar)  Presentación con diseño consistente: mismo estilo, tipografías, paleta de color, y márgenes.

**** Logo visible en portada y discretamente en el pie o esquina de todas las diapositivas ynúmero de diapositiva.

**** Textos resumidos: 3–5 bullets por slide, 6–10 palabras por bullet (máx.).**** Incluye material visual: diagrama ER, arquitectura, flujo UX, tablero Kanban/Gantt, evidencias

de pruebas.**** Número adecuado de diapositivas: 10–12 para 10 minutos (≈ 45–60 s/slide).

**** Ensayo cronometrado: 2 pasadas completas + 1 pasada de “preguntas”, desviación < 10%.**** No dependas de la presentación: guion memorizado por ideas (no por frases).

**** Lenguaje técnico y respetuoso, respuestas concretas (estructura: “respuesta → moƟvo →evidencia”).

**** Puntualidad: llegar 10–15 min antes, material listo (PDF + PPT + adaptador/HDMI + enlaceoffline).

### 2.2 Antipatrones que bajan nota (evítalos)  Leer las diapositivas o dar la espalda a la audiencia para mirar la pantalla.

Autora: Olga M. Moreno Martín

**** Demasiadas diapositivas o diapositivas “vacías” que no aportan valor.**** Exceso de texto y falta de imágenes/diagramas/infografías.

**** Faltas de ortografía, inconsistencias de términos, o promesas que no se ven en el producto.**** No respetar el tiempo: alargarse > 30% o quedarse corto sin cubrir pruebas y conclusiones.

## 3. Estructura recomendada de diapositivas (10–12 slides)

Esta estructura está pensada para: claridad, ritmo, evidencia y ajuste al tiempo. Incluye “qué mostrar” y “qué decir”.

| # | Diapositiva | Qué debe llevar (visual) | Qué decir (1 idea principal) | Tiempo |
| --- | --- | --- | --- | --- |
| 1 | Portada | Logo + nombre app + subtítulo + integrantes + ciclo/año | “Qué es la app y qué problema resuelve” | 0:15–0:20 |
| 2 | Índice | 6 puntos máximo | “Ruta de la presentación” | 0:10 |
| 3 | Introducción | Problema + usuario objetivo + contexto | “Por qué existe este proyecto” | 0:45 |

4 Objetivos 1 general + 4–6 específicos medibles “Cómo medimos que 0:45 está terminado”

5 Estado del arte 2–3 alternativas + hueco que cubrís “Qué hay y por qué 0:50 aportamos valor”

6 Tecnología Mapa “Por qué elegimos esta 0:55 (justificada) necesidad→tecnología→moƟvo stack, además de que os

lo haya dicho Olga” 7 Metodología + Kanban + mini Gantt + ramas Git “Cómo organizamos y 0:55

| 8 | Git Base de datos | Diagrama ER “icónico” explicar 3 | controlamos cambios” “Cómo garantizamos | 0:55 |
| --- | --- | --- | --- | --- |
| 9 | (ER) UX/UI | relaciones clave Capturas: Paleta (coincide | integridad” “Flujo del usuario en | 1:00 |

(pantallas) presentación) botones y pocos pasos” demostración: home, login, carrito,

perfil, admin. 10 Implementación Arquitectura (capas/servicios) + “Cómo se conecta todo” 0:55

endpoints clave 11 Decisiones de Auth + roles + middlewares + “Robustez, seguridad y 1:00

código transacciones + i18n consistencia” 12 Pruebas + Criterios aceptación + evidencias + 3 “Funciona y sabemos 2:00

Conclusiones + mejoras futuras por qué” Futuro

13 Demo Aplicación desplegada en local o “Te enseñamos que 5:00 servidor funciona, no creamos

usuarios, enseñamos producto”

Autora: Olga M. Moreno Martín

## 4. Guion de orador (texto breve por diapositiva)

Usa este guion como “ideas clave”. No lo leas: memoriza la intención de cada slide. Añade ejemplos concretos de vuestro proyecto.

### 1. Portada  Presentación en 1 frase: “Somos ___ y presentamos ___, una app que ___ para ___.”

**** Promesa de estructura: “En 10 min veremos necesidad, diseño, implementación yvalidación.”

### 2. Índice  “Seguiremos este orden para ir del problema al producto y evidencias de calidad.”

### 3. Introducción  Problema: 1 frase realista.

**** Usuario objetivo y escenario.**** Consecuencia si no se resuelve (tiempo, errores, abandono, etc.).

### 4. Objetivos  Objetivo general + objetivos específicos medibles (ej.: “registro seguro”, “carrito”, “admin

con roles”).**** Cierra con: “Estos objetivos guían nuestras pruebas de aceptación.”

### 5. Estado del arte  Menciona 2–3 alternativas y qué aportan.

**** Explica el hueco: vuestro caso necesita X (personalización, roles, trazabilidad, etc.).

### 6. Tecnología  Justifica por criterios: mantenibilidad, curva de aprendizaje, escalabilidad, seguridad.

**** Evita enumerar: conecta cada tecnología a una necesidad del proyecto.

### 7. Metodología + Git  Di explícitamente: “SCRUM no aplica por tamaño; usamos Kanban para flujo continuo.”

**** Explica ramas: main estable, develop integración, feature/* y PRs.**** Menciona Gantt como visión global y control de hitos.

### 8. Base de datos  Explica 3 entidades y 2 relaciones importantes.

**** Reglas de integridad: claves foráneas, unique email, índices.**** Si hay compras: “La operación es atómica mediante transacciones.”

### 9. UX/UI  Cuenta el flujo completo en 20–30 s (de entrar a comprar).

Autora: Olga M. Moreno Martín

**** Destaca decisiones: validación, mensajes de error, responsive, consistencia visual.**** Enseña admin y roles: “solo admins ven X”.

### 10. Implementación  Arquitectura: separación por capas (controlador/servicio/repositorio) o similar.

**** Lista 3 endpoints clave y qué devuelven.**** Menciona logging/errores y manejo de estados.

### 11. Decisiones de código  Auth: hashing + tokens/sesiones; roles y autorización.

**** Middlewares: validación, CORS, rate limit.**** Seguridad: sanitización, headers, no exponer secretos.

**** Transacciones: compra/pedido coherente (rollback si falla).**** i18n: ES/EN y formatos de moneda/fecha.

### 12. Pruebas + Conclusiones + Futuro  Presenta 3 criterios de aceptación (Given/When/Then) + evidencia (captura/log).

**** Conclusión: qué funciona y qué aprendiste (programación, BD, redes, calidad, doc).**** Futuro: 3 mejoras realistas (CI/CD, pagos reales, analítica, accesibilidad).

## 5. Guía de diseño para la presentación (para maximizar nota)

Sigue estas reglas para que la presentación sea limpia, profesional y fácil de evaluar.

### 5.1 Estilo visual recomendado  Tipografía: 1 familia (Calibri/Inter) — títulos 32–40 pt, cuerpo 20–24 pt.

**** Paleta: 2 colores base + 1 acento. Evita arcoíris y fondos con textura.**** Regla 6×6 (aprox.): hasta 6 bullets y 6–10 palabras por bullet.

**** Cada 2–3 slides, incluir 1 elemento visual (diagrama, captura, infografía).**** Alineación y márgenes constantes; deja “aire” (espacio en blanco).

**** Usa iconos coherentes (mismo set).

### 5.2 Material visual mínimo (obligatorio)  Diagrama ER de la base de datos (pictórico).

**** Esquema de arquitectura (Front/Back/BD) o diagrama de componentes.**** Capturas de las pantallas clave (login, carrito, admin).

**** Kanban + mini Gantt (visión general).**** Evidencia de pruebas de aceptación (tabla o capturas).

Autora: Olga M. Moreno Martín

## 6. Técnica de exposición (contacto visual, lenguaje y tiempo)

### 6.1 Método “3 capas” para hablar sin leer  Capa 1 (Titular): 1 frase por slide (“la idea”).

**** Capa 2 (Detalles): 2–3 datos o decisiones.**** Capa 3 (Evidencia): 1 prueba (captura, diagrama, test, métrica).

### 6.2 Control del tiempo (evitar desviaciones)  Ensaya con cronómetro: objetivo 9:30–10:00.

**** Marca “puntos de control”: al final de la slide 6 debes ir por ~4:00.**** Si te retrasas: recorta Estado del arte y UI, pero NO recortes pruebas.

**** Cierra siempre con 20–30 s para “futuro + gracias”.

### 6.3 Lenguaje técnico y respuestas concretas (Q&A)  Estructura de respuesta: Respuesta directa → MoƟvo → Evidencia del proyecto.

**** Si no sabes algo: “No lo implementamos por tiempo, pero la solución sería ___ por ___.”**** Evita muletillas y jerga informal; sé respetuoso y preciso.

**** Cuando te pregunten por decisiones: menciona trade-offs (ventaja y coste).

## 7. Pruebas de aceptación (plantillas listas para usar)

Incluye al menos 3–5 pruebas del flujo crítico. Añade evidencias (capturas/logs) en anexos o en la slide final.

| ID | Criterio de aceptación (Given/When/Then) | Evidencia |
| --- | --- | --- |
| PA-01 | Dado un usuario registrado, cuando inicia sesión con | Captura + registro de evento |

credenciales válidas, entonces accede al área privada.

PA-02 Dado un carrito con productos, Captura pedido + BD cuando confirma la compra,

entonces se genera un pedido con líneas y total correcto.

PA-03 Dado un usuario sin rol admin, Captura 403 + log cuando intenta acceder al panel

admin, entonces se bloquea con 403 y mensaje.

PA-04 Dado stock limitado, cuando dos Demostración + BD compras ocurren casi a la vez,

entonces no se permite stock negativo (transacción).

## 8. Preparación final (para el día de la defensa)

**** Exporta a PDF además del PPTX (evita problemas de fuentes).

Autora: Olga M. Moreno Martín

**** Ten la demo preparada offline o con plan B (capturas/vídeo corto).**** Comprueba ortografía y coherencia de términos (App/Proyecto, Usuario/Cliente).

**** Revisa que lo presentado coincide con lo implementado (no prometas features no hechas).**** Llega con antelación y prueba el proyector/sonido si aplica.

### 9. SUBIDA DE NOTA/RECUPERACIÓN EPDs

Puedes alcanzar hasta +3 puntos si estás aprobado en EPD.

Si estás suspenso en EPD, debes recuperar haciendo lo señalado en naranja, cada unidad didáctica que hayas suspendido:

UD1: Despliegue con Docker de la App Laravel con su stack completo, bbdd, phpmyadmin, etc. (+1) o desplegar en servidor de pago (Recomendado Digital Ocean).

UD2: Realizar la interfaz gráfica de la aplicación con Tailwind 4 (+1). Realizar un diseño de la aplicación de alta calidad, responsive, con UI/UX vendibles y profesionales.

UD3: Si has suspendido el proyecto, ya sabes … tiene que funcionar aquí y aprobarás. No hay subida, es parte de la nota de convocatoria.

UD4: Utilizar Redis para conectar con Laravel y crear Queues (+1). Realizar la práctica de MongoDB correctamente.

Nota: un alumno suspenso en Ev. continua en EPD, tiene que superar la defensa del proyecto y realizar la letra naranja de las Unidades didácticas suspensas, se seguirá el mismo esquema de

evaluación.

**Advertencia:** La presentación son 5 minutos por persona (si sois 3 en el proyecto), si sois 2personas 7,5 min cada uno; si un compañero habla más de forma evidente, o menos, de forma

evidente, podría calificarse como suspenso en cualquiera de los casos. A los 15 minutos la presentación termina, los puntos no vistos serán calificados como 0.

Autora: Olga M. Moreno Martín

### 10. Rúbrica

**Memoria correctamente maquetada según el modelo.**Diseño de la presentación adecuado (logo, imágenes, infografías, textos resumidos, número

**DEFENSA Y** adecuado de diapositivas)**15%PRESENTACIÓN** Exposición clara, con contacto visual, no dependiente de la presentación, lenguaje adecuado y

respetuoso, expresión corporal, respuestas claras y concretas. Puntualidad y ajuste al tiempo establecido.

**DEFINICION DEL** Está perfectamente definido y se percibe el alcance sin entrar en rodeos. Hay una versión en español**5%PROYECTO** y otra en inglés.

Está perfectamente definido y se percibe el alcance sin entrar en rodeos. Los objetivos son**RESUMEN Y OBJETIVOS 10%** coherentes y están identificados.

**ANÁLISIS Y** Los requisitos están completamente desarrollados en tareas, están agrupados coherentemente y hay**ESPECIFICACIÓN DE 5%** una medición de tiempo o dificultad razonable.

REQUISITOS

La solución está completamente descrita, también gráficamente y el diseño es profesional. Los**PROPUESTA DE** recursos son adecuados al alcance del problema. **El tamaño y nivel de complejidad son adecuados**

20% SOLUCIÓN para un TFG, tiene un nivel de detalle elevado, refleja una lógica de negocio real o su diseño refleja

un caso real.

Presenta una planificación previa y otra planificación real valorando las desviaciones. El plan refleja**PLAN DE TRABAJO 5%** claramente es desarrollo de los objetivos, tareas y pruebas con tiempos razonablemente realistas.

El código o los ficheros fuentes siguen las normas de buenas prácticas. La solución está estructurada,**DESARROLLO DE LA** se percibe la reutilización y calidad de la solución. No presenta incidencias no controladas. Se

20%SOLUCIÓN _cumplen todos los objetivos._ El diseño funciona, es escalable y real, refleja y aprovecha la

tecnología actual y real, en este caso Laravel

| DESPLIEGUE E INSTALACIÓN | 10% Se documenta y demuestra el proceso de despliegue de manera completa, hay planes de contingencia y definición de requisitos mínimos. |
| --- | --- |
| EVOLUCIÓN Y TRABAJO FUTURO | 5% Se describe completamente la evolución del proyecto, se describen las ampliaciones y trabajos futuros de manera realista, definiendo el alcance y valorando el coste. |

Es completa, describe claramente el recurso usado en cada apartado de la memoria, sigue las normas APA.

BIBLIOGRAFIA 5%
