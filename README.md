# Sistema Financiero Contable – Ingeniería Web - Grupo 1SF132
Carlos Delgado, Diego Jaen, Mauricio Parra, Edwin Zhong

Proyecto académico desarrollado para la asignatura **Ingeniería Web** de la Universidad Tecnológica de Panamá.  
El objetivo del sistema es permitir a una organización **registrar sus transacciones contables**, mantener un **catálogo de cuentas** y generar automáticamente **informes financieros** (Estado de Resultados y Balance General), con control de usuarios y firmas de informes.

---

## 📌 Objetivos del sistema

- Registrar transacciones contables en un **Diario General** con partida doble.
- Mantener un **catálogo de cuentas** clasificado por Activos, Pasivos, Patrimonio, Ingresos y Gastos.
- Generar automáticamente:
  - **Estado de Resultados** (Ingresos, Gastos, Utilidad Neta).
  - **Balance General** (Activos, Pasivos, Patrimonio), verificando la ecuación:
    > Activo = Pasivo + Patrimonio
- Administrar **usuarios y roles** con distintos niveles de acceso (Administrador, Contador, Gerente Financiero, Auditor).
- Permitir la **firma de informes** por el Gerente Financiero y marcar informes modificados como no confiables.
- Mantener **auditoría** básica de operaciones (creación de cuentas, cambios en saldos, etc.).

---

## ✅ Requisitos del Sistema (según rúbrica)

### Requisitos funcionales principales

- **Login y sesiones**
  - Autenticación de usuarios administrativos.
  - Validación de credenciales con contraseña encriptada.
  - Solo usuarios activos pueden acceder al sistema.

- **Módulo de Usuarios**
  - CRUD de usuarios administrativos (crear, actualizar, consultar).
  - Posibilidad de activar/desactivar usuarios en lugar de eliminarlos.
  - Relación con roles.

- **Módulo de Roles y Permisos**
  - Roles: Administrador, Contador, Gerente Financiero, Auditor.
  - Asignación de uno o más roles a cada usuario.
  - Control de alcance por módulo según rol.

- **Catálogo de Cuentas (CRUD)**
  - Crear, editar, listar y desactivar cuentas contables.
  - Clasificación por:
    - Clase 1: Activo
    - Clase 2: Pasivo
    - Clase 3: Patrimonio
    - Clase 4: Ingresos
    - Clase 5: Gastos
    - Clase 6: Costos
    - Clase 7: Otros gastos
  - Registro de:
    - Usuario que crea la cuenta (`created_by`).
    - Fecha de creación (`created_at`).

- **Diario General y Transacciones**
  - Registro de transacciones contables con **partida doble**:
    - Cuenta de débito, cuenta de crédito y monto.
  - Asociación de la transacción al usuario que la registra (`created_by`).
  - Triggers en BD que:
    - Validan que la transacción esté balanceada (suma débitos = suma créditos).
    - Actualizan el saldo de las cuentas afectadas.

- **Informes Financieros**
  - **Estado de Resultados**:
    - Usa únicamente cuentas de clases 4, 5, 6 y 7.
    - Calcula Ingresos, Gastos/Costos y Utilidad Neta del período.
  - **Balance General**:
    - Usa únicamente cuentas de clases 1, 2 y 3.
    - Muestra Activos, Pasivos y Patrimonio a una fecha determinada.
    - Verifica la ecuación Activo = Pasivo + Patrimonio.

- **Firma de Informes (Gerente Financiero)**
  - El Gerente Financiero puede **firmar** un informe generado.
  - Se almacena en BD:
    - el contenido del informe (JSON),
    - un `hash` de integridad,
    - la firma asociada al usuario y fecha.
  - Si se modifican datos que afectan un informe firmado, el informe anterior se puede marcar como **modificado / no confiable** mediante `is_modified`.

- **Página pública**
  - Página pública que explica:
    - el propósito del sistema,
    - la importancia de registrar transacciones contables,
    - acceso al formulario de login.

- **Control de errores y validación**
  - Uso de:
    - Clase de conexión a BD (`Connection`) mediante PDO.
    - Clases `Validator` y `Sanitizer` para validar y sanitizar datos de entrada.
    - `ErrorHandler` y `ErrorHandlerInterface` para manejo centralizado de errores.
    - `Logger` para guardar errores en archivos de log.

### Requisitos no funcionales

- Sistema desarrollado en **PHP** con **MAMP** (macOS) y **MySQL**.
- Arquitectura por capas:
  - Controladores (Controllers),
  - Servicios (Services),
  - Repositorios (Repositories),
  - Modelos (Models),
  - Seguridad (Security),
  - Utilidades (Utils).
- Documentación técnica con **diagramas UML**:
  - Casos de uso,
  - Diagrama de clases,
  - Diagramas de secuencia (login, transacción, firma),
  - Diagrama de estados de la transacción,
  - Diagrama de componentes,
  - Diagrama E/R de base de datos.
- Estilos con **CSS** propio para evitar penalización de la rúbrica.

---

## 🧰 Tecnologías utilizadas

- **Lenguaje backend:** PHP 8+
- **Servidor local:** MAMP (macOS)
- **Base de datos:** MySQL (MariaDB en MAMP)
- **ORM / acceso a datos:** PDO nativo con clase `Connection`
- **Frontend:** HTML5, CSS3, JavaScript básico
- **Control de versiones:** Git + GitHub
- **Modelado UML:** Herramienta basada en código (p.ej. Mermaid) y diagramas exportados a PNG
- **Logs:** archivos en `storage/logs`

---

## 🗄️ Base de Datos

El repositorio **incluye el script completo de la base de datos**, tal como exige la rúbrica.

- Archivo SQL:  
  `docs/database/sistema_financiero.sql`

Este script:

- Crea la base de datos `sistema_financiero`.
- Crea las tablas:
  - `users`, `roles`, `user_roles`, `role_permissions`
  - `accounts`, `transactions`, `transaction_lines`
  - `reports`, `report_signatures`, `audit_logs`
- Crea vistas:
  - `view_income_statement`
  - `view_balance_sheet`
- Crea triggers para:
  - validar partida doble (`check_balanced_transaction`),
  - actualizar saldos de cuentas (`update_account_balance`),
  - auditar cambios en cuentas (`audit_account_changes`).
- Inserta datos iniciales:
  - Roles básicos (Administrador, Contador, Gerente Financiero, Auditor),
  - Usuario administrador por defecto (`admin`),
  - Catálogo de cuentas contable básico.

---

## ⚙️ Instalación y configuración

### 1. Requisitos previos

- macOS con **MAMP** instalado.
- PHP 8.x (incluido con MAMP).
- Navegador web moderno.

### 2. Clonar el repositorio

```bash
git clone https://github.com/diegojaen18/sistema-financiero.git
cd sistema-financiero
