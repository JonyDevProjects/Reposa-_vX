# Roadmap: Estrategia de Testing Integral y Pruebas Unitarias de Alto Valor — Reposa+

## Contexto y Justificación

Este documento establece la planificación técnica, fundamentación epistemológica y catálogo de implementación para la estrategia de aseguramiento de la calidad (*Quality Assurance - QA*) de **Reposa+**. Define formalmente la postura arquitectónica del proyecto ante el debate clásico de la **Pirámide de Pruebas de Mike Cohn (2009)** frente a los modelos modernos de la industria como el **Trofeo de Pruebas (*Testing Trophy*) de Kent C. Dodds y Martin Fowler**, proporcionando el marco metodológico y la batería de pruebas necesarias para la defensa del Trabajo de Fin de Grado (TFG).

En el desarrollo de Reposa+, el ecosistema de pruebas se configuró inicialmente bajo un enfoque *Integration-First* para blindar la integridad transaccional del e-commerce (concurrencia de stock mediante `lockForUpdate`, persistencia relacional en MySQL y transacciones ACID). Como consecuencia, la suite creció de manera asimétrica:
* **Pruebas de Integración (`tests/Feature/`):** 90 pruebas exhaustivas cubriendo controladores, base de datos, middlewares y servicios.
* **Pruebas de Extremo a Extremo (`Playwright`):** 8 pruebas completas sobre navegador Chromium real validando flujos de compra, Google OAuth y pasarela Stripe Checkout.
* **Pruebas Unitarias (`tests/Unit/`):** Prácticamente vacías (1 prueba trivial `ExampleTest`), generando una aparente vulnerabilidad metodológica frente a evaluadores académicos formados en la pirámide tradicional.

Este roadmap resuelve dicha asimetría en dos vertientes complementarias:
1. **Fundamentación Teórica Rigurosa:** Justificar por qué en un e-commerce transaccional el núcleo de máximo retorno de inversión (*ROI*) reside en la integración y no en tests unitarios con mocks ficticios.
2. **Implementación de Pruebas Unitarias de Alto Valor:** Identificar, aislar e implementar pruebas unitarias puras sobre la lógica algorítmica y el dominio en memoria (máquina de estados finitos, motor de tarifas logísticas y reglas de cálculo), completando una pirámide de pruebas tripartita inexpugnable.

**Fecha de creación:** 10 de septiembre de 2026  
**Autor:** Jonathan Quispe  
**Rama activa de trabajo:** `feature/guest-checkout-and-shipping`  
**Documentos de referencia:**
* [`docs/progreso/roadmap-diseno-ecommerce.md`](./roadmap-diseno-ecommerce.md) (Estructura de referencia de roadmaps del proyecto).
* [`docs/progreso/roadmap-flujos-checkout-paqueteria-oauth.md`](./roadmap-flujos-checkout-paqueteria-oauth.md) (Especificación de flujos de compra y paquetería).
* [`docs/Manual_Desarrollador_CICD.md`](../Manual_Desarrollador_CICD.md) (Guía de entornos y ejecución de suites).
* [`docs/Memoria_Proyecto.md`](../Memoria_Proyecto.md) (Memoria académica oficial del TFG).

---

## Matriz Comparativa: Pirámide Clásica vs. Trofeo de Pruebas en Reposa+

```
   PIRÁMIDE CLÁSICA (Mike Cohn, 2009)            TROFEO DE PRUEBAS MODERNO (Dodds/Fowler, 2018-2024)
         (Paradigma Pre-Docker)                        (Arquitectura Adoptada en Reposa+)

                 ▲                                              ┌──────────┐
                / \     E2E (Pocos)                             │   E2E    │  (8 tests Playwright)
               /───\                                         ┌──┴──────────┴──┐
              /     \   Integración (Medios)                 │  INTEGRACIÓN   │  (90 tests Feature)
             /───────\                                       │ (Feature Tests)│  ← MÁXIMO VALOR Y ROI
            /         \ Unitarios (Muchos)                   └──┬──────────┬──┘
           ─────────────                                        │ UNITARIOS│  (20+ tests Dominio Puro)
                                                                └──────────┘
```

| Dimensión de Análisis | Pirámide Clásica (Cohn, 2009) | Trofeo de Pruebas en Reposa+ (2026) | Justificación en el Dominio E-commerce |
|---|---|---|---|
| **Premisa Histórica** | Levantar bases de datos o servidores era prohibitivamente lento (minutos por test). | Los contenedores Docker ejecutan 90 tests de integración contra MySQL en **1.9 segundos**. | La supuesta lentitud de las pruebas de integración es un mito superado por la tecnología moderna. |
| **Peligro de los Mocks** | Se promueve el uso masivo de *mocks* para aislar clases individuales. | Los mocks ocultan fallos de claves foráneas, bloqueos pesimistas y restricciones relacionales. | Un mock nunca detecta una sobreventa por condición de carrera (*race condition*). La base de datos real sí. |
| **Retorno de Inversión (ROI)** | Mayor volumen en la base porque eran los tests más baratos de escribir. | Mayor volumen en integración porque es donde ocurren los fallos críticos de negocio. | Si el checkout falla en producción, el negocio pierde dinero. La integración garantiza la coherencia transaccional. |
| **Rol de los Unit Tests** | Cubrir cada método, getter y setter de cada clase del sistema. | Cubrir algoritmos puros, máquinas de estado y lógica matemática de dominio en memoria. | Evita el antipatrón de testear implementaciones triviales o duplicar el código con aserciones redundantes. |

---

## Estado del Roadmap de Estrategia de Testing

| Fase | Denominación / Enfoque | Entregables Clave | Estado |
|:---:|---|---|:---:|
| **1** | Fundamentación Epistemológica y Análisis de Fallos | Justificación teórica frente a la Pirámide de Cohn, taxonomía de errores transaccionales y matriz de ROI de pruebas. | ⏳ Planificada |
| **2** | Especificación de Pruebas Unitarias de Alto Valor | Diseño de casos de prueba puros para máquina de estados (`Order`), cálculo logístico (`Courier`) y dominio en memoria. | ⏳ Planificada |
| **3** | Implementación de la Suite Unitaria Pura (`tests/Unit`) | Creación de clases de test heredando de `PHPUnit\Framework\TestCase`, sin kernel de base de datos, ejecución sub-10ms. | ⏳ Planificada |
| **4** | Consolidación y Certificación de la Pirámide Tripartita | Ejecución conjunta (Unit + Feature + E2E), reporte unificado de cobertura y actualización de CI/CD. | ⏳ Planificada |
| **5** | Argumentario Académico y Guion de Defensa para el TFG | Guion de preguntas y respuestas críticas para la defensa ante el tribunal universitario y formalización en memoria. | ⏳ Planificada |

---

## Fase 1: Fundamentación Epistemológica y Análisis de Fallos

**Objetivo:** Articular la justificación teórica y empírica de por qué la estrategia de pruebas de Reposa+ se concentra en la integración transaccional, demostrando la inviabilidad de confiar exclusivamente en pruebas unitarias en un sistema financiero/e-commerce.

### 1.1 El Mito de la Lentitud de la Integración en la Era de la Virtualización Ligera
- Documentar cómo la arquitectura de contenedores (Docker Engine + MySQL 8 en memoria/volúmenes locales) permite que la suite de 90 tests de integración (`tests/Feature/`) se complete en **1.97 segundos**.
- En 2009, cuando Mike Cohn formuló la pirámide, una suite de integración equivalente requería varios minutos debido a I/O en disco mecánico y motores relacionales pesados. Hoy en día, la velocidad de ejecución de la integración en Reposa+ es de ~21 ms por prueba, haciendo innecesario el sobre-aislamiento artificial.

### 1.2 Taxonomía de Errores Críticos en E-commerce y Límites de las Pruebas Unitarias
Demostrar por qué los fallos catastróficos de una tienda online escapan por completo a las pruebas unitarias tradicionales:
1. **Condiciones de Carrera (*Race Conditions*) en Stock:**
   - Si dos clientes adquieren la última unidad simultáneamente, un test unitario con mocks siempre responderá `stockAvailable() = true` para ambos hilos.
   - Solo un test de integración contra el motor InnoDB ejecutando `SELECT ... FOR UPDATE` dentro de `DB::transaction()` garantiza que el segundo hilo sufra un bloqueo y sea rechazado con rollback atómico ([`CheckoutStockTest.php`](../../Reposa+/tests/Feature/CheckoutStockTest.php)).
2. **Restricciones de Integridad Referencial (*Foreign Keys & Cascades*):**
   - El borrado de carritos, la anulación de usuarios o el guardado de snapshots de dirección (`addresses`, `order_items`) dependen de las restricciones declaradas en el DDL de MySQL. Los mocks de PHPUnit ignoran las violaciones de claves foráneas.
3. **Seguridad Perimetral y Autorización HTTP:**
   - La denegación de facturas PDF sin token criptográfico (**HTTP 403 Forbidden**) reside en la interacción del Router, el Middleware y el Kernel HTTP. Un test unitario sobre el método del controlador omite la tubería (*pipeline*) de seguridad.
4. **Sincronización Asíncrona de Webhooks de Pago:**
   - El procesamiento de `checkout.session.completed` de Stripe requiere verificar la idempotencia de base de datos para evitar cobros dobles o dobles decrementos de stock ante reintentos de red.

### 1.3 Criterios de Demarcación: ¿Cuándo Unitario y cuándo Integración?
Establecer la regla formal de arquitectura de software para Reposa+:
* **Es Test Unitario (`tests/Unit/`) SI:** La lógica evaluada es una función matemática pura, un autómata de estados finitos, una transformación de cadenas o una regla de negocio evaluable en memoria sin tocar base de datos, sistema de archivos, red ni el contenedor de dependencias de Laravel.
* **Es Test de Integración (`tests/Feature/`) SI:** La operación involucra persistencia relacional (SQL), transacciones ACID, bloqueo de concurrencia, despacho de eventos/correos, middleware de autenticación o contratos de inyección de dependencias.

---

## Fase 2: Especificación de Pruebas Unitarias de Alto Valor

**Objetivo:** Identificar y diseñar formalmente las suites de pruebas unitarias que aporten valor genuino de verificación algorítmica sin incurrir en redundancia ni en tests triviales de getters/setters.

### 2.1 Suite 1: Autómata de Estados Finitos del Pedido (`OrderStateUnitTest`)
- **Clase bajo prueba:** [`App\Models\Order`](../../Reposa+/app/Models/Order.php)
- **Naturaleza unitaria:** Las reglas de transición de estados y terminalidad están definidas en la constante inmutable `Order::ALLOWED_TRANSITIONS` y se resuelven mediante métodos estáticos puros (`canTransition()`, `getAllowedTransitions()`).
- **Casos de prueba a aislar:**
  1. `test_pending_valid_transitions`: Verificar que `pending` puede transicionar exclusivamente a `processing`, `completed` (manual) y `cancelled`.
  2. `test_processing_cannot_skip_to_completed`: Demostrar que `processing` no puede saltar directamente a `completed`, garantizando el paso obligado por `shipped` (defecto histórico corregido).
  3. `test_logistics_pipeline_progression`: Validar la cadena `shipped` $\rightarrow$ `delivered` $\rightarrow$ `completed`.
  4. `test_terminal_states_have_no_forward_transitions`: Comprobar que `cancelled` y `refunded` devuelven arrays vacíos de transiciones permitidas.
  5. `test_status_color_mapping_integrity`: Comprobar que cada estado de `STATUSES` posee un color semántico válido asignado en `STATUS_COLORS`.

### 2.2 Suite 2: Motor de Tarifas y Algoritmos de Paquetería (`ShippingRateCalculatorUnitTest`)
- **Clase bajo prueba:** [`App\Services\Shipping\MockStandardCourierService`](../../Reposa+/app/Services/Shipping/MockStandardCourierService.php)
- **Naturaleza unitaria:** El cálculo de gastos de envío y la generación de identificadores de seguimiento son funciones algorítmicas deterministas que no requieren conexión con base de datos.
- **Casos de prueba a aislar:**
  1. `test_free_shipping_threshold_at_50_euros`: Carritos con importe total $\ge 50.00\text{ €}$ deben computar tarifa estándar a coste `0.00 €`.
  2. `test_standard_shipping_rate_below_threshold`: Carritos con importe total $< 50.00\text{ €}$ deben computar exactamente `4.95 €`.
  3. `test_fixed_shipping_rates`: Opciones `express_24h` (`7.95 €`) y `pickup_point` (`3.50 €`) deben retornar tarifas fijas independientes del total.
  4. `test_tracking_number_format_compliance`: Todo número de seguimiento generado debe coincidir estrictamente con la expresión regular `^RPX\d{4}\d{6}ES$` (ej: `RPX2026849201ES`).
  5. `test_business_day_delivery_calculation`: El cálculo de entrega estimada debe excluir fines de semana (sábado y domingo).

### 2.3 Suite 3: Lógica de Dominio y Resolución de Identidad en Memoria (`OrderDomainLogicUnitTest`)
- **Clase bajo prueba:** [`App\Models\Order`](../../Reposa+/app/Models/Order.php)
- **Naturaleza unitaria:** Evaluación de atributos virtuales y métodos de conveniencia instanciando el modelo en memoria con `new Order([...])`, sin ejecutar `$order->save()`.
- **Casos de prueba a aislar:**
  1. `test_is_guest_returns_true_when_user_id_is_null`: Instancia sin `user_id` debe retornar `true`.
  2. `test_is_guest_returns_false_when_user_id_is_present`: Instancia con `user_id = 42` debe retornar `false`.
  3. `test_customer_name_prioritizes_shipping_name_snapshot`: Si existe `shipping_name`, debe prevalecer sobre el nombre del usuario relacionado.
  4. `test_customer_email_prioritizes_shipping_email_snapshot`: Si existe `shipping_email`, debe prevalecer sobre el correo del usuario relacionado.
  5. `test_customer_name_fallback_for_empty_data`: Si no hay nombre de envío ni usuario, debe retornar el fallback `'Cliente Reposa+'`.

### 2.4 Suite 4: Formateo y Reglas de Producto (`ProductDomainUnitTest`)
- **Clase bajo prueba:** [`App\Models\Product`](../../Reposa+/app/Models/Product.php)
- **Naturaleza unitaria:** Comprobaciones de disponibilidad de inventario y manipulación de precios unitarios en memoria.
- **Casos de prueba a aislar:**
  1. `test_in_stock_determination`: Métodos o accessors de stock evalúan correctamente valores $> 0$ vs $= 0$.
  2. `test_price_precision_and_calculations`: Operaciones aritméticas de subtotal (`price * quantity`) sin errores de coma flotante.

---

## Fase 3: Implementación de la Suite Unitaria Pura (`tests/Unit`)

**Objetivo:** Materializar las clases de prueba en `Reposa+/tests/Unit/`, asegurando que hereden de `PHPUnit\Framework\TestCase` (el test runner puro de PHPUnit sin bootstrapping de base de datos) para garantizar velocidades de ejecución de microsegundos.

### 3.1 Estructura de Ficheros a Implementar

```
Reposa+/tests/Unit/
├── ExampleTest.php                    (Test base heredado)
├── OrderStateUnitTest.php             (Máquina de estados finitos: 6 tests, 25 aserciones)
├── ShippingRateCalculatorUnitTest.php (Cálculo de tarifas y tracking: 5 tests, 18 aserciones)
├── OrderDomainLogicUnitTest.php       (Lógica de dominio y snapshots: 5 tests, 12 aserciones)
└── ProductDomainUnitTest.php          (Formatos y stock en memoria: 3 tests, 8 aserciones)
```

### 3.2 Directrices Técnicas de Implementación
* **Aislamiento Estricto:** Prohibido el uso de `RefreshDatabase`, llamadas a `DB::`, consultas Eloquent (`Order::create()`) o peticiones HTTP (`$this->get()`).
* **Instanciación en Memoria:** Los modelos se crearán con `new Order([...])` o mediante mocks ligeros si fuera necesario.
* **Tiempo Máximo de Ejecución:** La suite unitaria completa debe ejecutarse en menos de **50 milisegundos**.

---

## Fase 4: Consolidación y Certificación de la Pirámide Tripartita

**Objetivo:** Consolidar las tres capas de pruebas del proyecto, verificar su ejecución armónica en entornos locales y automatizados, y certificar las métricas globales para la memoria del TFG.

### 4.1 Balance de la Pirámide Tripartita de Reposa+

```
┌───────────────────────────────┬──────────────┬──────────────┬────────────────────────────────────────────────────────┐
│ Nivel de Prueba               │ Directorio   │ Nº Pruebas   │ Tiempo / Tecnologías                                   │
├───────────────────────────────┼──────────────┼──────────────┼────────────────────────────────────────────────────────┤
│ **Pruebas de Sistema (E2E)**  │ `e2e/`       │ 8 tests      │ ~10s / Playwright, Chromium real, Nginx LB, Stripe    │
│ **Pruebas de Integración**    │ `tests/Feature`│ 90 tests   │ ~1.9s / Laravel, MySQL 8, InnoDB, Redis, MailHog       │
│ **Pruebas Unitarias**         │ `tests/Unit` │ 19+ tests    │ ~15ms / PHPUnit puro, lógica de dominio en memoria     │
└───────────────────────────────┴──────────────┴──────────────┴────────────────────────────────────────────────────────┘
```

### 4.2 Integración en CI/CD (`.github/workflows/ci.yml`)
- Asegurar que el pipeline de integración continua ejecute tanto la suite unitaria como la de integración de forma transparente:
  ```bash
  php artisan test --testsuite=Unit
  php artisan test --testsuite=Feature
  ```

---

## Fase 5: Argumentario Académico y Guion de Defensa para el TFG

**Objetivo:** Dotar al estudiante de un repertorio dialéctico irrebatible ante las preguntas del tribunal relativas a calidad del software, arquitectura de pruebas y patrones de diseño.

### 5.1 Preguntas Críticas Anticipadas y Respuestas Modelo

#### Pregunta 1: *"¿Por qué la proporción de pruebas en su proyecto tiene más peso en Integración que en Unitarias, contraviniendo la clásica pirámide de Mike Cohn?"*
> **Respuesta:**  
> *"La pirámide de Mike Cohn data de 2009, una época donde ejecutar pruebas contra bases de datos requería minutos por prueba debido a limitaciones de hardware y motores relacionales pesados. En la actualidad, autores de referencia en la Ingeniería del Software como **Martin Fowler** y **Kent C. Dodds (Testing Trophy)** han demostrado que en aplicaciones transaccionales y e-commerce modernos, el mayor retorno de inversión (*ROI*) se sitúa en la **capa de integración**.*  
> *En Reposa+, un test unitario con mocks no puede verificar si un bloqueo pesimista `SELECT ... FOR UPDATE` previene la sobreventa de stock en compras concurrentes, ni si una clave foránea en cascada preserva la integridad de la base de datos. Nuestra suite de integración ejecuta 90 pruebas completas en apenas 1.9 segundos gracias a la contenerización Docker, ofreciendo máxima fidelidad real a velocidad de test unitario. Reservamos las pruebas unitarias para donde realmente aportan valor: el autómata de estados finitos y el cálculo logístico."*

#### Pregunta 2: *"¿Qué lógica de negocio ha considerado estrictamente unitaria y por qué?"*
> **Respuesta:**  
> *"Hemos aplicado un principio de demarcación riguroso: es unitario todo algoritmo determinista sin efectos colaterales fuera del espacio de memoria del proceso. Concretamente, en `tests/Unit/` aislamos:*  
> *1. La **máquina de estados finitos** de los pedidos (`Order`), verificando matemáticamente el grafo dirigido de transiciones permitidas y terminales.*  
> *2. El **motor de tarifas logísticas** (`MockStandardCourierService`), comprobando los umbrales de gratuidad de 50€ y la expresión regular de tracking `RPX2026...ES`.*  
> *3. Los **métodos de dominio en memoria**, evaluando la resolución de identidad de invitados (`isGuest`) y fallbacks de snapshots sin persistencia relacional."*

#### Pregunta 3: *"¿Por qué no utilizó mocks masivos para convertir los Feature Tests en Unit Tests?"*
> **Respuesta:**  
> *"Porque el uso excesivo de mocks en flujos transaccionales introduce el antipatrón de **falsa confianza**: se prueba la especificación del mock y no el comportamiento del sistema. Como describe la literatura, un mock de Eloquent simula que la base de datos responde con éxito, pero en producción una restricción de clave foránea no detectada o una consulta N+1 puede tumbar el servidor. Preferimos probar contra el motor MySQL real en integración y reservar el aislamiento unitario para la lógica pura."*

---

## Registro de Cambios y Trazabilidad

| Fecha | Autor | Versión | Resumen de Cambios |
|---|---|:---:|---|
| **10/09/2026** | Jonathan Quispe | `v1.0.0` | Creación del roadmap integral de estrategia de testing, justificación epistemológica frente a la Pirámide de Cohn (Testing Trophy), especificación del catálogo de pruebas unitarias puras y guion de defensa para el TFG. |
