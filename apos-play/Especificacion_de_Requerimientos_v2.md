# Proyecto AposPlay

# Documento de Requisitos del Sistema

**Versión 2.0**

**Fecha: 14/03/2026**

Realizado por: Ivasiuta, Mariano - Martinez, Alejandro
Realizado para: Cliente

---

## Lista de Cambios

| Nro | Fecha       | Descripcion              | Autor   |
|-----|------------|--------------------------|---------|
| 0   | 15/06/2025 | Version 1.0              | Equipo  |
| 1   | 14/03/2026 | Actualización v2.0 — sincronización con código implementado. Se corrigieron UCs parciales, se marcaron UCs pendientes, se agregaron UCs 21-23 y módulo de complejos. | Equipo  |

---

## Indice

- [Participantes del Proyecto](#participantes-del-proyecto)
- [Objetivos del sistema](#objetivos-del-sistema)
- [Requisitos del Sistema](#requisitos-del-sistema)
  - [Requisitos de Informacion](#requisitos-de-informacion)
  - [Requisitos Funcionales](#requisitos-funcionales)
    - [Diagrama de Casos de Usos](#diagrama-de-casos-de-usos)
    - [Definicion de Actores](#definicion-de-actores)
    - [Caso de Usos del Sistema](#caso-de-usos-del-sistema)
  - [Requisitos No funcionales](#requisitos-no-funcionales)
- [Matriz de Rastreabilidad Objetivo/Requisitos](#matriz-de-rastreabilidad-objetivorequisitos)
- [Glosario de Terminos](#glosario-de-terminos)

---

## Presentacion General

AposPlay es una aplicacion web basada en Laravel 12 que permite a clubes y complejos deportivos gestionar de manera integral la reserva de canchas de futbol 5 y padel en la ciudad de Apostoles, Misiones. El sistema pretende reemplazar las planillas telefonicas y hojas de calculo actuales por un flujo de trabajo centralizado que:

- Ofrece a los usuarios finales la posibilidad de registrarse, consultar la disponibilidad en tiempo real y pagar sus reservas en linea.
- Brinda a los administradores herramientas de administracion de canchas, control de agenda, reportes y programas de fidelizacion.
- Integra pasarelas de pago (MercadoPago) y canales de notificacion (correo) para asegurar la cobertura completa del ciclo de vida de la reserva.
- Implementa un sistema de roles (superadmin, owner, staff, usuario) con paneles diferenciados por rol y gestion de complejos deportivos.

---

## Participantes del Proyecto

Desarrolladores:
- Ivasiuta, Mariano
- Martinez, Alejandro

Cliente:
- Duenos de canchas de futbol 5 y padel, personal de recepcion (Staff), usuarios que reservan.

---

## Objetivos del sistema

| ID     | Nombre                                  | Descripcion                                                                                         | Estabilidad | Comentarios |
|--------|----------------------------------------|------------------------------------------------------------------------------------------------------|-------------|-------------|
| OBJ-01 | Gestionar reservas en linea            | Permitir la creacion, modificacion y cancelacion de reservas de canchas en tiempo real.               | Alta        | Ninguno     |
| OBJ-02 | Maximizar la ocupacion                 | Optimizar los horarios libres y reducir tiempos muertos entre reservas mediante notificaciones y pago anticipado. | Media       | Ninguno     |
| OBJ-03 | Facilitar la administracion de canchas | Habilitar ABM de canchas, horarios de atencion y bloqueos especiales.                                | Alta        | Ninguno     |
| OBJ-04 | Gestionar pagos y reembolsos           | Procesar cobros, senas y devoluciones de forma segura.                                               | Alta        | Ninguno     |
| OBJ-05 | Proveer metricas y reportes            | Ofrecer estadisticas de ocupacion e ingresos para la toma de decisiones.                             | Media       | Ninguno     |
| OBJ-06 | Fidelizar a los clientes               | Otorgar puntos y promociones canjeables por tiempo de juego.                                         | Media       | Ninguno     |
| OBJ-07 | Gestionar roles y permisos             | Permitir la administracion de roles (superadmin, owner, staff, user) con acceso diferenciado por panel. | Alta        | Nuevo en v2 |

---

## Requisitos del Sistema

### Requisitos de Informacion

| ID     | Nombre                          | Objetivos asociados                                          | Requisitos asociados                                          | Descripcion                                                                    | Datos especificos                                                                                                                              | Estabilidad |
|--------|---------------------------------|-------------------------------------------------------------|--------------------------------------------------------------|--------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|-------------|
| IRQ-01 | Informacion de canchas          | OBJ-03 Facilitar la administracion de canchas               | UC-06, UC-07, UC-08, UC-16                                   | El sistema debera almacenar la informacion correspondiente a las canchas.      | Nombre de la cancha, Tipo (Futbol/padel), Precio por hora, Estado (Activa/inactiva), Horarios por dia, Bloqueos especiales, Complejo asociado. | Alta        |
| IRQ-02 | Informacion de reservas         | OBJ-01 Gestionar reservas en linea, OBJ-02 Maximizar la ocupacion | UC-03, UC-04, UC-09, UC-10                                   | El sistema debera almacenar la informacion correspondiente a las reservas.     | Fecha, Hora de inicio/fin, Cancha, Usuario, Estado, Pago asociado, Puntos canjeados.                                                           | Alta        |
| IRQ-03 | Informacion de usuarios         | OBJ-01 Gestionar reservas en linea, OBJ-06 Fidelizar a los clientes | UC-01, UC-05, UC-18, UC-19                                   | El sistema debera almacenar la informacion correspondiente a los usuarios.     | Perfil, Datos de contacto, Preferencias de notificacion, Saldo de puntos, Rol (superadmin/owner/staff/user).                                   | Media       |
| IRQ-04 | Informacion de pagos y reembolsos | OBJ-04 Gestionar pagos y reembolsos, OBJ-05 Proveer metricas y reportes | UC-11, UC-12, UC-17                                           | El sistema debera almacenar la informacion correspondiente a pagos y reembolsos. | Monto, moneda, medio de pago, estado, referencia pasarela, fecha.                                                                              | Alta        |
| IRQ-05 | Informacion de metricas de uso  | OBJ-05 Proveer metricas y reportes                          | UC-16, UC-17                                                 | El sistema debera almacenar la informacion correspondiente a metricas de uso.  | Utilizacion por cancha, ingresos agrupados, ranking de clientes.                                                                               | Baja        |
| IRQ-06 | Informacion de complejos        | OBJ-07 Gestionar roles y permisos                           | UC-21, UC-22, UC-23                                          | El sistema debera almacenar la informacion correspondiente a los complejos deportivos. | Nombre, direccion, owner (FK users), estado activo/inactivo, canchas asociadas, staff asignado (pivot complex_staff).                           | Alta        |

---

### Requisitos Funcionales

- UC-01 -- Gestionar Usuarios
- UC-02 -- Ver disponibilidad de canchas
- UC-03 -- Reservar cancha
- UC-04 -- Cancelar reserva
- UC-05 -- Ver mis reservas
- UC-06 -- Crear cancha
- UC-07 -- Definir horarios de atencion
- UC-08 -- Bloquear horarios especiales **(Pendiente de implementacion)**
- UC-09 -- Modificar reserva **(Pendiente de implementacion)**
- UC-10 -- Confirmar asistencia
- UC-11 -- Pagar reserva online
- UC-12 -- Reembolsar pago
- UC-13 -- Enviar recordatorio de juego
- UC-14 -- Notificar cancelacion **(Pendiente de implementacion)**
- UC-15 -- Crear cupones / descuentos
- UC-16 -- Ver reporte de ocupacion
- UC-17 -- Exportar ingresos
- UC-18 -- Acumular puntos
- UC-19 -- Canjear puntos
- UC-20 -- Gestionar promociones
- UC-21 -- Gestionar Roles (superadmin)
- UC-22 -- Gestionar Staff (owner)
- UC-23 -- Panel por Rol

---

#### Diagrama de Casos de Usos

*[Pendiente de actualizacion — los diagramas de la v1.0 corresponden a una plantilla de otro proyecto y deben ser reemplazados por diagramas que reflejen el sistema AposPlay con los 23 UCs y los 6 actores actuales.]*

Subsistemas del sistema AposPlay:
- Gestion de usuarios
- Reservas
- Administracion de canchas
- Pagos
- Notificaciones y Automatizaciones
- Fidelizacion y Promociones
- Roles y Permisos (nuevo en v2)

---

#### Definicion de Actores

| ID     | Nombre            | Descripcion                                                                                          | Comentarios |
|--------|-------------------|------------------------------------------------------------------------------------------------------|-------------|
| ACT-01 | Usuario           | Este actor representa a las personas que reservan y pagan una cancha. Rol: `user`.                   | ninguno     |
| ACT-02 | Superadmin        | Administrador global del sistema. Crea cuentas de owner, gestiona todos los recursos. Rol: `superadmin`. | Renombrado de "Administrador" en v2 |
| ACT-03 | Staff             | Empleado que confirma asistencia in-situ y gestiona reservas del dia. Acceso scoped al complejo asignado. Rol: `staff`. | ninguno     |
| ACT-04 | Sistema de pagos  | Pasarela externa (MercadoPago).                                                                      | ninguno     |
| ACT-05 | Job Scheduler     | Componente que ejecuta tareas programadas (cron job de Laravel).                                     | ninguno     |
| ACT-06 | Owner             | Dueno de complejos deportivos. Gestiona sus propios complejos y staff. Rol: `owner`.                 | Nuevo en v2 |

---

#### Caso de Usos del Sistema

---

| | **UC-01** |
|---|---|
| **Nombre** | **Gestionar Usuarios** |
| **Objetivos asociados** | OBJ-01 Gestionar reservas en linea |
| **Requisitos asociados** | IRQ-03 Informacion de usuarios |
| **Descripcion** | El sistema permite que un usuario cree una cuenta nueva o inicie sesion con credenciales existentes. |
| **Precondicion** | El usuario no esta autenticado. |
| **Secuencia normal** | |
| | 1. El usuario selecciona "Registrarse" o "Iniciar sesion". |
| | 2. El sistema muestra el formulario. |
| | 3. El usuario completa el formulario. |
| | 4. El usuario envia las credenciales completadas en el formulario. |
| | 5. El sistema valida la informacion recibida e inicia la sesion. |
| **Postcondicion** | El usuario accede al Home del sistema. |
| **Excepciones** | Paso 1: Si el usuario ingreso credenciales invalidas, el usuario lo notifica y vuelve a abrir el formulario. |
| **Rendimiento** | Paso 5: 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | Se recomienda reCAPTCHA y verificacion de email opcional. El sistema incluye verificacion de email y recuperacion de contrasena. |

---

| | **UC-02** |
|---|---|
| **Nombre** | **Ver disponibilidad de canchas** |
| **Objetivos asociados** | OBJ-01 Gestionar reservas en linea, OBJ-02 Maximizar la ocupacion |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | El sistema muestra un calendario con los intervalos libres y ocupados para cada cancha seleccionada. |
| **Precondicion** | El usuario esta autenticado. Se requiere al menos una cancha activa. |
| **Secuencia normal** | |
| | 1. El usuario elige la fecha y el tipo de cancha. |
| | 2. El sistema consulta disponibilidad. |
| | 3. El sistema muestra en pantalla una lista de las canchas disponibles en la fecha seleccionada. Mostrando: Nombre de la cancha, Horas libres, Direccion de la cancha, Precio por hora. |
| **Postcondicion** | El usuario accede a la lista de disponibilidad de canchas. |
| **Excepciones** | Paso 2: Si falla la consulta a la Base de datos, el sistema notifica un mensaje de error indicando que vuelva a intentar. |
| **Rendimiento** | Paso 2: Menos de 2 segundos |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | Se pueden agregar otros filtros de busqueda. El sistema muestra los proximos 7 dias a partir de manana. |

---

| | **UC-03** |
|---|---|
| **Nombre** | **Reservar cancha** |
| **Objetivos asociados** | OBJ-01 Gestionar reservas en linea, OBJ-02 Maximizar la ocupacion |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | Permite al usuario generar una reserva de una cancha. |
| **Precondicion** | El usuario esta autenticado. La disponibilidad de la cancha esta confirmada. |
| **Secuencia normal** | |
| | 1. El usuario selecciona la cancha disponible en la que desea jugar. |
| | 2. El sistema muestra en pantalla la informacion completa de la cancha pidiendo al usuario que indique la hora y cantidad de horas que desea reservar la cancha. |
| | 3. El usuario selecciona la hora especifica de la reserva e indica la cantidad de horas que desea reservar. |
| | 4. El usuario envia la solicitud de reserva. |
| | 5. El sistema verifica la disponibilidad de la hora y cantidad de hora ingresada por el usuario. |
| | 6. El sistema crea la reserva con estado "Pendiente de pago" y bloquea la disponibilidad de la cancha en ese horario. |
| | 7. El sistema muestra un mensaje de "Cancha reservada con exito". |
| **Postcondicion** | Reserva registrada. Disponibilidad de cancha bloqueada en esa fecha y horario. |
| **Excepciones** | Paso 5: No es posible reservar la cantidad de horas ingresadas en dicho horario, se le pide reintentar. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | Se puede agregar la opcion de cancelacion automatica de reserva si en 2 horas no realiza el pago completo o sena. Soporta entre 1 y 4 horas de duracion. Si el usuario tiene cupones o puntos disponibles, puede aplicarlos como descuento. |

---

| | **UC-04** |
|---|---|
| **Nombre** | **Cancelar reserva** |
| **Objetivos asociados** | OBJ-01 Gestionar reservas en linea, OBJ-02 Maximizar la ocupacion |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | Permite al usuario cancelar una reserva con al menos 24 horas de anticipacion. |
| **Precondicion** | El usuario esta autenticado. La reserva de la cancha esta confirmada. Faltan al menos 24 horas para la hora reservada. |
| **Secuencia normal** | |
| | 1. El usuario accede a la seccion "Mis reservas". |
| | 2. El sistema muestra en pantalla el historial de reservas solicitadas por el usuario. |
| | 3. El usuario selecciona la reserva confirmada que desea cancelar. |
| | 4. El sistema muestra en pantalla la informacion de la reserva. |
| | 5. El usuario selecciona la opcion de cancelar reserva. |
| | 6. El sistema verifica si es posible cancelar la reserva. |
| | 7. El sistema le consulta mediante un cuadro de dialogo si esta seguro de cancelar la reserva. |
| | 8. El usuario confirma que esta seguro de cancelar la reserva. |
| | 9. El sistema modifica el estado de la reserva a "Cancelada" y redirige la pantalla a la vista de reservas del usuario y le notifica que la reserva se cancelo correctamente. |
| **Postcondicion** | Reserva cancelada. Disponibilidad de cancha libre en esa fecha y horario. |
| **Excepciones** | Paso 6: No es posible cancelar la reserva porque faltan menos de 24 horas para la hora solicitada, se le notifica el error. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | Si la reserva estaba pagada, se simula el reembolso automatico via MercadoPago y se actualiza el payment_status a "refunded". |

---

| | **UC-05** |
|---|---|
| **Nombre** | **Ver mis reservas** |
| **Objetivos asociados** | OBJ-01 Gestionar reservas en linea |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | El sistema permite que un usuario consulte las reservas realizadas. |
| **Precondicion** | El usuario esta autenticado. El tiene al menos alguna reserva realizada. |
| **Secuencia normal** | |
| | 1. El usuario accede a la seccion "Mis reservas". |
| | 2. El sistema muestra en pantalla el historial de reservas realizadas por el usuario. |
| | 3. El usuario selecciona una reserva especifica. |
| | 4. El sistema muestra en pantalla la informacion correspondiente de dicha reserva, mostrando fecha y hora de la reserva, Cancha, Direccion, cantidad de horas, precio y estado. |
| **Postcondicion** | El usuario accede a la informacion e la reserva. |
| **Excepciones** | Paso 2: El usuario no tiene ninguna reserva realizada. |
| **Rendimiento** | Paso 2: menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-06** |
|---|---|
| **Nombre** | **Crear cancha** |
| **Objetivos asociados** | OBJ-03 Facilitar la administracion de canchas |
| **Requisitos asociados** | IRQ-01 Informacion de canchas |
| **Descripcion** | Permite al owner crear y editar la informacion de las canchas posibles para su reservacion, dentro del contexto de un complejo deportivo. |
| **Precondicion** | El usuario esta autenticado como owner o superadmin. Tiene al menos un complejo registrado. |
| **Secuencia normal** | |
| | 1. El owner accede a la seccion "Mis complejos". |
| | 2. El sistema muestra en pantalla un listado con los complejos registrados por el owner. |
| | 3. El owner selecciona un complejo y accede a la gestion de canchas. |
| | 4. El sistema muestra en pantalla un formulario que pide: nombre, direccion, precio, tipo (Futbol/Padel), cantidad de jugadores permitidos. |
| | 5. El owner completa el formulario y presiona "Guardar cancha". |
| | 6. El sistema verifica la informacion ingresada por el owner. |
| | 7. El sistema le consulta mediante un cuadro de dialogo si esta seguro de guardar la cancha. |
| | 8. El usuario que esta seguro de guardar la cancha. |
| | 9. El sistema registra la nueva cancha y redirige la pantalla a la vista de canchas del complejo y le notifica que la cancha se guardo correctamente. |
| **Postcondicion** | Cancha registrada dentro del complejo. |
| **Excepciones** | Paso 6: No es posible registrar la cancha por errores al completar el formulario, el sistema le notifica al owner y le pide ingresar los datos correctos. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | La gestion de canchas se realiza dentro del contexto de un complejo deportivo (ver UC-23 y modelo Complex). |

---

| | **UC-07** |
|---|---|
| **Nombre** | **Definir horarios de atencion** |
| **Objetivos asociados** | OBJ-03 Facilitar la administracion de canchas |
| **Requisitos asociados** | IRQ-01 Informacion de canchas |
| **Descripcion** | Permite al administrador registrar los horarios de atencion de sus canchas. |
| **Precondicion** | El usuario esta autenticado como administrador. Tiene al menos una cancha registrada. |
| **Secuencia normal** | |
| | 1. El administrador accede a la seccion "Mis canchas". |
| | 2. El sistema muestra en pantalla un listado con las canchas registradas por el administrador. |
| | 3. El administrador selecciona la opcion "Agregar horarios" de la cancha correspondiente. |
| | 4. El sistema muestra en pantalla un formulario que pide: horario de apertura y cierre. Soporta hasta 2 turnos por dia (manana y tarde). |
| | 5. El administrador completa el formulario y presiona "Guardar horario". |
| | 6. El sistema verifica la informacion ingresada por el administrador. |
| | 7. El sistema le consulta mediante un cuadro de dialogo si esta seguro de guardar el horario. |
| | 8. El usuario confirma que esta seguro de guardar el horario. |
| | 9. El sistema registra el nuevo horario y redirige la pantalla a la vista de canchas del administrador y le notifica que el horario se guardo correctamente. |
| **Postcondicion** | Horario de cancha registrada. |
| **Excepciones** | Paso 6: No es posible registrar el horario de la cancha por errores al completar el formulario, el sistema le notifica al administrador y le pide ingresar los datos correctos. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-08** |
|---|---|
| **Nombre** | **Bloquear horarios especiales** |
| **Objetivos asociados** | OBJ-03 Facilitar la administracion de canchas |
| **Requisitos asociados** | IRQ-01 Informacion de canchas |
| **Descripcion** | Permite al administrador cerrar temporalmente canchas por mantenimiento o eventos. |
| **Precondicion** | El usuario esta autenticado como administrador. Tiene al menos una cancha registrada y con horarios de atencion definidos. |
| **Estado** | **PENDIENTE DE IMPLEMENTACION** |
| **Secuencia normal** | |
| | 1. El administrador accede a la seccion "Mis canchas". |
| | 2. El sistema muestra en pantalla un listado con las canchas registradas por el administrador. |
| | 3. El administrador selecciona la opcion "Cierre temporal" de la cancha correspondiente. |
| | 4. El sistema muestra en pantalla un formulario que pide: rango de fecha, rango horario y motivo del cierre temporal de la cancha. |
| | 5. El administrador completa el formulario y presiona "Cerrar cancha". |
| | 6. El sistema verifica la informacion ingresada por el administrador. |
| | 7. El sistema le consulta mediante un cuadro de dialogo si esta seguro de guardar el cierre de la cancha. |
| | 8. El usuario que esta seguro de guardar el cierre temporal. |
| | 9. El sistema registra el bloqueo de horario de la cancha y redirige la pantalla a la vista de canchas del administrador y le notifica que la cancha se cerro correctamente para esa fecha y horario. |
| **Postcondicion** | Los usuarios no pueden reservar la cancha en el rango de fecha y hora bloqueado. |
| **Excepciones** | Paso 6: No es posible registrar el bloqueo de horario de la cancha por errores al completar el formulario, el sistema le notifica al administrador y le pide ingresar los datos correctos. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-09** |
|---|---|
| **Nombre** | **Modificar reserva** |
| **Objetivos asociados** | OBJ-01 Gestionar reservas, OBJ-02 Maximizar la ocupacion |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | Permite al usuario reprogramar una reserva existente con al menos 4 horas de anticipacion. |
| **Precondicion** | El usuario esta autenticado. Tiene al menos una reserva confirmada. |
| **Estado** | **PENDIENTE DE IMPLEMENTACION** |
| **Secuencia normal** | |
| | 1. El usuario accede a la seccion "Mis reservas". |
| | 2. El sistema muestra en pantalla el historial de reservas realizadas por el usuario. |
| | 3. El usuario selecciona una reserva especifica. |
| | 4. El sistema muestra en pantalla la informacion correspondiente de dicha reserva, mostrando fecha y hora de la reserva, Cancha, Direccion, cantidad de horas, precio y estado. |
| | 5. El usuario presiona "Modificar reserva". |
| | 6. El sistema muestra un formulario que pide: Fecha, hora y cantidad de horas para la nueva reserva. |
| | 7. El usuario completa el formulario y lo envia. |
| | 8. El sistema valida la informacion ingresada por el usuario. |
| | 9. El sistema modifica la reserva existente y redirige la pantalla a la vista de reservas del usuario y le notifica que la reserva se modifico correctamente. |
| **Postcondicion** | La reserva tiene otro horario y fecha confirmada. |
| **Excepciones** | Paso 8: No es posible modificar la reserva en la fecha y hora seleccionada porque otro usuario lo reservo mientras completaba el formulario, el sistema le notifica este error y le solicita ingresar otra fecha y hora o continuar con la reserva anterior. |
| **Rendimiento** | Paso 8: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-10** |
|---|---|
| **Nombre** | **Confirmar asistencia** |
| **Objetivos asociados** | OBJ-02 Maximizar la ocupacion, OBJ-05 Proveer metricas |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | Permite al Staff marcar la asistencia de los jugadores de la reserva correspondiente. El Staff puede confirmar la asistencia cambiando el estado de la reserva de "Pagada" a "Confirmada". |
| **Precondicion** | El usuario esta autenticado como staff. Es la hora de la reserva. La reserva esta en estado "Pagada". |
| **Secuencia normal** | |
| | 1. El Staff accede a la seccion "Reservas del dia". |
| | 2. El sistema muestra en pantalla el listado con las reservas del dia. Resalta la reserva actual. |
| | 3. El Staff selecciona marcar asistencia ("Confirmar"). |
| | 4. El sistema muestra un cuadro de dialogo preguntando si esta seguro de confirmar la asistencia de los jugadores de la reserva. |
| | 5. El staff confirma la asistencia. |
| | 6. El sistema muestra un mensaje que marco la asistencia exitosamente y redirige a la pantalla de "Reservas del dia". |
| **Postcondicion** | La reserva tiene estado "Confirmada" (asistida). |
| **Excepciones** | Paso 6: No se puede marcar la asistencia porque no es el horario correspondiente de la reserva, el sistema le notifica esto al staff y le solicita que espere a que sea el horario correspondient. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-11** |
|---|---|
| **Nombre** | **Pagar reserva online** |
| **Objetivos asociados** | OBJ-04 Gestionar pagos y reembolsos |
| **Requisitos asociados** | IRQ-04 Informacion de Pagos y reembolsos |
| **Descripcion** | Permite al usuario realizar el pago de la cancha. |
| **Precondicion** | Reserva en estado "Pendiente de pago". |
| **Secuencia normal** | |
| | 1. El Usuario accede a la seccion "Mis reservas". |
| | 2. El sistema muestra en pantalla el listado con las reservas del usuario. Resalta la reserva actual. |
| | 3. El Usuario selecciona "Pagar reserva" en la reserva actual resaltada. |
| | 4. El sistema muestra la pasarela de pago (MercadoPago). |
| | 5. El Usuario confirma el pago de la reserva. |
| | 6. El sistema muestra un mensaje que se pago la reserva correctamente y redirige a la pantalla de "Mis reservas". |
| **Postcondicion** | La reserva tiene estado "Pagada". Se registra payment_id, amount_paid y payment_status. |
| **Excepciones** | Paso 3: El usuario no tiene ninguna reserva. No se resalta una reserva especifica y termina el caso de uso. Paso 6: El sistema no pudo procesar el pago correspondiente. Se muestra un mensaje advirtiendo esto y explicando el motivo. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-12** |
|---|---|
| **Nombre** | **Reembolsar pago** |
| **Objetivos asociados** | OBJ-04 Gestionar pagos y reembolsos |
| **Requisitos asociados** | IRQ-04 Informacion de Pagos y reembolsos |
| **Descripcion** | Permite al staff devolver el total o parcial del importe pagado. |
| **Precondicion** | Reserva en estado "Cancelado" con al menos 8 o 2 horas de anticipacion. |
| **Secuencia normal** | |
| | 1. El Staff accede a la seccion "Reservas del dia". |
| | 2. El sistema muestra en pantalla el listado con las reservas del dia. |
| | 3. El staff selecciona la reserva correspondiente para reembolsar el dinero. |
| | 4. Segun la anticipacion: 1) Si la reserva se cancela con al menos 8 horas de anticipacion - ver contrato "Reembolsar pago completo". 2) Si la reserva se cancela con menos de 8 horas y con al menos 2 horas de anticipacion justificada - Ver contrato "Reembolsar pago parcial". |
| | 5. El Staff confirma el reembolso de la reserva. |
| | 6. El sistema muestra un mensaje que se reembolso correctamente y redirige a la pantalla de "Reservas del dia". |
| **Postcondicion** | Se registra el reembolso en el historial de pagos. |
| **Excepciones** | Paso 4: Si la reserva se cancela con menos de 2 horas de anticipacion, el sistema muestra un mensaje al Staff advirtiendo esto y no permite el reembolso. Si la reserva se marca como "No asistida", el sistema muestra un mensaje al Staff advirtiendo que no permite el reembolso. Paso 6: El sistema no pudo procesar el reembolso correspondiente. Se muestra un mensaje advirtiendo esto y explicando el motivo. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | El reembolso via API de MercadoPago esta actualmente simulado (TODO pendiente). |

**Contrato C-01: Reembolsar pago completo**

| | |
|---|---|
| **Descripcion** | Permite al staff devolver el total del importe pagado. |
| **Precondicion** | Reserva en estado "Cancelado" con al menos 8 horas de anticipacion. |
| **Secuencia normal** | 1. El sistema muestra un mensaje notificando que la reserva se cancelo con al menos 8 horas de anticipacion y se puede reembolsar el importe total de la reserva. Muestra un boton de "Reembolsar importe total". |
| | 2. El Staff selecciona el boton "Reembolsar importe total". |
| | 3. El sistema muestra la pasarela de pago. |

**Contrato C-02: Reembolsar pago parcial**

| | |
|---|---|
| **Descripcion** | Permite al staff devolver el importe parcial pagado. |
| **Precondicion** | Reserva en estado "Cancelado" entre 8 y 2 horas de anticipacion. |
| **Secuencia normal** | 1. El sistema muestra un mensaje notificando que la reserva se cancelo con menos de 8 horas de anticipacion y solamente se puede reembolsar un parcial del importe de la reserva. Muestra un boton de "Reembolsar importe parcial". |
| | 2. El Staff selecciona el boton "Reembolsar importe parcial". |
| | 3. El sistema muestra la pasarela de pago. |

---

| | **UC-13** |
|---|---|
| **Nombre** | **Enviar recordatorio de juego** |
| **Objetivos asociados** | OBJ-02 Maximizar la ocupacion |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | Job Scheduler notifica 24 h y 1 h antes. |
| **Precondicion** | Reserva en estado "Confirmado". |
| **Secuencia normal** | |
| | 1. Cron identifica reservas proximas (ventana de 24h y 1h). |
| | 2. Cron envia emails de recordatorio de la reserva via notificacion GameReminder. Incluye logica de de-duplicacion para evitar envios repetidos dentro de 2 horas. |
| **Postcondicion** | Usuario notificado. |
| **Excepciones** | - |
| **Rendimiento** | Paso 2: Menos de 1 segundo |
| **Frecuencia** | varias veces al dia (job se ejecuta cada hora) |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-14** |
|---|---|
| **Nombre** | **Notificar cancelacion** |
| **Objetivos asociados** | OBJ-02 Maximizar la ocupacion |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | Envia aviso de cancelacion y libera horario. |
| **Precondicion** | La reserva cambia de estado a "Cancelado". |
| **Estado** | **PENDIENTE DE IMPLEMENTACION** |
| **Secuencia normal** | |
| | 1. El sistema detecta el evento de cancelacion de la reserva. |
| | 2. El sistema envia la notificacion de la cancelacion de reserva al Staff. |
| **Postcondicion** | Staff notificado. |
| **Excepciones** | - |
| **Rendimiento** | Paso 2: Menos de 30 segundos desde el evento. |
| **Frecuencia** | varias veces al dia |
| **Estabilidad** | alta |
| **Comentarios** | |

---

| | **UC-15** |
|---|---|
| **Nombre** | **Crear cupones/descuentos** |
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Informacion de reservas |
| **Descripcion** | Permite al Administrador generar cupones de descuentos para clientes. |
| **Precondicion** | El usuario esta autenticado como administrador (superadmin u owner). |
| **Secuencia normal** | |
| | 1. El Administrador accede a la seccion "Cupones y descuentos". |
| | 2. El sistema muestra en pantalla una lista con todos los cupones creados. Muestra un boton "Crear cupon". |
| | 3. El sistema muestra un formulario donde le pide completar: porcentaje de descuento o monto fijo, clientes a los que aplican al cupon, descripcion del cupon, fechas de inicio/fin, cantidad maxima de usos. |
| | 4. El administrador completa este formulario y presiona "Guardar". |
| | 5. El sistema le muestra un mensaje preguntando si esta seguro de guardar el cupon. |
| | 6. El administrador confirma. |
| | 7. El sistema crea el cupon y aplica el cupon a los clientes seleccionados en el paso 4. |
| | 8. El sistema envia una notificacion a los clientes aplicados informando que pueden usar este cupon en su proxima reserva. |
| | 9. El sistema redirige a "Cupones y descuentos". |
| **Postcondicion** | Cupon creado. El cliente recibe la notificacion del cupon. |
| **Excepciones** | Paso 7: El sistema no pudo crear el cupon. Muestra el error. |
| **Rendimiento** | Paso 8: Menos de 1 segundo. |
| **Frecuencia** | pocas veces al mes |
| **Estabilidad** | baja |
| **Comentarios** | Los cupones pueden ser de tipo porcentaje o monto fijo. Se asignan a usuarios seleccionados manualmente. Los cupones se pueden activar/desactivar. |

---

| | **UC-16** |
|---|---|
| **Nombre** | **Ver reporte de ocupacion** |
| **Objetivos asociados** | OBJ-05 Proveer metricas y reportes |
| **Requisitos asociados** | IRQ-05 Informacion de metricas de uso |
| **Descripcion** | Permite al Administrador visualizar estadisticas sobre la utilizacion de las canchas (por cancha, franja horaria, dia, semana, mes). |
| **Precondicion** | El usuario debe estar autenticado como Administrador. |
| **Secuencia normal** | |
| | 1. El administrador accede a la seccion "Reportes". |
| | 2. El sistema muestra opciones de filtrado por cancha, fecha o tipo. Incluye presets rapidos (hoy, semana, mes). |
| | 3. El administrador selecciona los filtros deseados y confirma. |
| | 4. El sistema procesa la informacion y muestra el reporte visual. Tipos de desglose: por cancha, por horario, por dia de semana, por semana, por mes. |
| **Postcondicion** | Se muestran estadisticas de ocupacion de las canchas. |
| **Excepciones** | Paso 4: Si no hay datos para el filtro seleccionado, el sistema informa que no hay informacion disponible. |
| **Rendimiento** | Paso 4: Menos de 2 segundos |
| **Frecuencia** | Varias veces por semana |
| **Estabilidad** | Alta |
| **Comentarios** | |

---

| | **UC-17** |
|---|---|
| **Nombre** | **Exportar ingresos** |
| **Objetivos asociados** | OBJ-05 Proveer metricas y reportes |
| **Requisitos asociados** | IRQ-04 Informacion de pagos y reembolsos |
| **Descripcion** | Permite al Administrador exportar en formato CSV o PDF los ingresos obtenidos en un periodo determinado. |
| **Precondicion** | El usuario debe estar autenticado como Administrador. |
| **Secuencia normal** | |
| | 1. El administrador accede a la seccion "Ingresos". |
| | 2. El sistema muestra opciones de filtrado por mes, ano o rango de fechas. |
| | 3. El administrador selecciona el periodo deseado y el formato de exportacion (CSV o PDF). |
| | 4. El sistema genera el archivo y permite su descarga. CSV incluye: ID, Fecha, Cancha, Usuario, Email, Precio total, Descuento, Monto pagado, Estado, Payment Status, MP ID, Tipo. PDF incluye totales de ingresos y reembolsos. |
| **Postcondicion** | Archivo de ingresos exportado correctamente. |
| **Excepciones** | Paso 4: Si no existen ingresos en el periodo seleccionado, se informa al administrador. |
| **Rendimiento** | Paso 4: Menos de 5 segundos |
| **Frecuencia** | 1 vez por mes (minimo) |
| **Estabilidad** | Alta |
| **Comentarios** | CSV incluye BOM UTF-8 para compatibilidad con Excel. PDF usa orientacion horizontal con libreria barryvdh/laravel-dompdf. |

---

| | **UC-18** |
|---|---|
| **Nombre** | **Acumular puntos** |
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-03 Informacion de usuarios |
| **Descripcion** | El sistema asigna puntos de fidelidad al usuario por cada reserva pagada. |
| **Precondicion** | El usuario debe haber pagado una reserva correctamente. |
| **Secuencia normal** | |
| | 1. El usuario realiza y paga una reserva. |
| | 2. El sistema registra la transaccion. |
| | 3. El sistema asigna automaticamente puntos al usuario segun la politica vigente (5 puntos por reserva, configurable en config/loyalty.php). |
| **Postcondicion** | El usuario acumula puntos en su perfil. |
| **Excepciones** | Paso 2: Si la reserva es cancelada o reembolsada, los puntos se revierten. El Observer previene duplicacion de puntos. |
| **Rendimiento** | Paso 3: Menos de 1 segundo |
| **Frecuencia** | Varias veces al dia |
| **Estabilidad** | Alta |
| **Comentarios** | Implementado via ReservationObserver que escucha cambios de status. Modelo LoyaltyPoint con tipos: earned, spent, reversed, expired. |

---

| | **UC-19** |
|---|---|
| **Nombre** | **Canjear puntos** |
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-03 Informacion de usuarios |
| **Descripcion** | El usuario puede aplicar puntos acumulados como credito al momento de realizar una nueva reserva. |
| **Precondicion** | El usuario tiene saldo de puntos suficiente (50 puntos minimo). |
| **Secuencia normal** | |
| | 1. El usuario inicia una nueva reserva. |
| | 2. El sistema le ofrece la opcion de aplicar puntos como descuento, mostrando el saldo actual. |
| | 3. El usuario selecciona usar puntos y confirma la reserva. |
| | 4. El sistema descuenta los puntos y refleja el nuevo precio. 50 puntos = 30% de descuento (configurable). |
| **Postcondicion** | Reserva creada y puntos descontados. Se registran points_redeemed, points_discount y final_price en la reserva. |
| **Excepciones** | Paso 4: Si el usuario no tiene puntos suficientes, se le informa y se continua sin descuento. |
| **Rendimiento** | Paso 4: Menos de 1 segundo |
| **Frecuencia** | Pocas veces al mes por usuario |
| **Estabilidad** | Media |
| **Comentarios** | El descuento por puntos se puede combinar con cupones. |

---

| | **UC-20** |
|---|---|
| **Nombre** | **Gestionar promociones** |
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-03 Informacion de usuarios |
| **Descripcion** | Permite al Administrador crear o editar reglas de promocion como combos, cupones o puntos extra. |
| **Precondicion** | El usuario esta autenticado como Administrador (superadmin, owner o staff). |
| **Secuencia normal** | |
| | 1. El administrador accede a la seccion "Promociones". |
| | 2. El sistema muestra promociones vigentes y permite crear nuevas. Incluye busqueda y toggle de estado activo/inactivo. |
| | 3. El administrador completa un formulario con la regla: tipo (combo/cupon/puntos extra), valor descuento, puntos bonus, duracion (fecha inicio/fin), condiciones (JSON). |
| | 4. El sistema guarda la promocion y la aplica automaticamente cuando corresponda. |
| **Postcondicion** | Nueva promocion registrada y activa. |
| **Excepciones** | Paso 4: Si la promocion es invalida o se superpone con otra del mismo tipo en el mismo rango de fechas, el sistema lo notifica. |
| **Rendimiento** | Paso 2: Menos de 2 segundos |
| **Frecuencia** | Pocas veces al mes |
| **Estabilidad** | Media |
| **Comentarios** | Validacion de conflictos via PromotionService::validatePromotion(). Policy restringe acceso por rol. |

---

| | **UC-21** |
|---|---|
| **Nombre** | **Gestionar Roles (superadmin)** |
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Informacion de usuarios, IRQ-06 Informacion de complejos |
| **Descripcion** | El superadmin puede crear cuentas de owner, ver todos los owners y sus complejos, y desactivar/reactivar owners. |
| **Precondicion** | El usuario esta autenticado como superadmin. |
| **Secuencia normal** | |
| | 1. El superadmin accede a la seccion "Owners" (/admin/owners). |
| | 2. El sistema muestra un listado de todos los owners con sus complejos asociados. Incluye busqueda por nombre/email. |
| | 3. El superadmin puede crear un nuevo owner seleccionando "Crear owner" (/admin/owners/crear). |
| | 4. El sistema muestra un formulario con: nombre, email, contrasena, complejo asociado (opcional). |
| | 5. El superadmin completa el formulario y confirma. |
| | 6. El sistema crea la cuenta con rol "owner" via RoleService::createOwner(). |
| | 7. El superadmin puede desactivar un owner (bloquea acceso sin borrar datos) o reactivarlo. |
| **Postcondicion** | Cuenta de owner creada/desactivada/reactivada. |
| **Excepciones** | Paso 6: Si el email ya existe, el sistema muestra un error de validacion. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | Pocas veces al mes |
| **Estabilidad** | Alta |
| **Comentarios** | Nuevo en v2. Implementado en App\Livewire\Admin\Owners\. |

---

| | **UC-22** |
|---|---|
| **Nombre** | **Gestionar Staff (owner)** |
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Informacion de usuarios, IRQ-06 Informacion de complejos |
| **Descripcion** | El owner puede crear cuentas de staff, asignarlas a sus complejos y revocar acceso. El staff solo ve el complejo al que esta asignado. |
| **Precondicion** | El usuario esta autenticado como owner o superadmin. Tiene al menos un complejo registrado. |
| **Secuencia normal** | |
| | 1. El owner accede a la seccion "Staff" (/owner/staff). |
| | 2. El sistema muestra un listado del staff asignado a los complejos del owner. Incluye busqueda por nombre/email. |
| | 3. El owner puede crear un nuevo staff seleccionando "Crear staff" (/owner/staff/crear). |
| | 4. El sistema muestra un formulario con: nombre, email, contrasena, complejo(s) a asignar. |
| | 5. El owner completa el formulario y confirma. |
| | 6. El sistema crea la cuenta con rol "staff" y la asigna al complejo via RoleService::createStaff() y assignStaffToComplex(). |
| | 7. El owner puede remover un staff de un complejo especifico. |
| **Postcondicion** | Cuenta de staff creada y asignada a complejo(s). |
| **Excepciones** | Paso 6: Si el email ya existe, el sistema muestra un error de validacion. |
| **Rendimiento** | Paso 6: Menos de 1 segundo |
| **Frecuencia** | Pocas veces al mes |
| **Estabilidad** | Alta |
| **Comentarios** | Nuevo en v2. Implementado en App\Livewire\Owner\Staff\. El owner solo puede gestionar staff de sus propios complejos. El superadmin puede gestionar staff de todos los complejos. La relacion staff-complejo se gestiona via tabla pivot complex_staff. |

---

| | **UC-23** |
|---|---|
| **Nombre** | **Panel por Rol** |
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Informacion de usuarios |
| **Descripcion** | El sistema presenta paneles diferenciados segun el rol del usuario autenticado, con acceso restringido mediante middleware. |
| **Precondicion** | El usuario esta autenticado. |
| **Secuencia normal** | |
| | 1. El usuario accede al sistema. |
| | 2. El sistema identifica el rol del usuario (superadmin, owner, staff, user). |
| | 3. El sistema muestra el panel correspondiente con las opciones disponibles para ese rol: |
| | - **Superadmin** (/admin/*): Acceso total. Gestiona owners, promociones, cupones, reportes, ingresos. |
| | - **Owner** (/owner/*): Gestiona sus complejos y staff. Accede a cupones, reportes, promociones. |
| | - **Staff** (/staff/*): Ve reservas del dia scoped a su complejo asignado. Accede a promociones. |
| | - **User** (/mis-reservas, /court-availability, /mis-puntos): Ve sus reservas, disponibilidad y puntos. |
| | 4. El middleware `role:` bloquea el acceso a rutas no autorizadas para el rol. |
| **Postcondicion** | El usuario ve unicamente las funciones correspondientes a su rol. |
| **Excepciones** | Paso 4: Si el usuario intenta acceder a una ruta no autorizada, el middleware retorna HTTP 403. |
| **Rendimiento** | Paso 2: Menos de 1 segundo |
| **Frecuencia** | Cada inicio de sesion |
| **Estabilidad** | Alta |
| **Comentarios** | Nuevo en v2. Middleware registrado en bootstrap/app.php como alias 'role'. Sidebar dinamico segun rol en resources/views/components/layouts/app/sidebar.blade.php. |

---

### Requisitos No funcionales

| ID     | Nombre       | Objetivos asociados                                                      | Requisitos asociados | Descripcion                                                                                       | Comentarios |
|--------|-------------|--------------------------------------------------------------------------|---------------------|---------------------------------------------------------------------------------------------------|-------------|
| NFR-01 | Seguridad    | OBJ-06 Fidelizar a los clientes                                         | -                   | El sistema debera incorporar algun mecanismo que permita la proteccion CSRF. Provisto por defecto por Laravel. | ninguno     |
| NFR-02 | Rendimiento  | OBJ-06 Fidelizar a los clientes                                         | -                   | El sistema debera permitir un rendimiento aceptable para una cantidad demandante de usuarios concurrentes. | ninguno     |
| NFR-03 | Usabilidad   | OBJ-06 Fidelizar a los clientes                                         | -                   | El sistema tendra un diseno responsivo. Implementado con Tailwind CSS v4 y Flux UI.               | ninguno     |
| NFR-04 | Auditoria    | OBJ-03 Facilitar la administracion de canchas, OBJ-05 Proveer metricas y reportes | -                   | El sistema debera permitir al administrador ver la auditoria con las acciones de cada usuario en el sistema. **PENDIENTE DE IMPLEMENTACION.** | ninguno     |

---

## Matriz de Rastreabilidad Objetivo/Requisitos

|        | OBJ-01 | OBJ-02 | OBJ-03 | OBJ-04 | OBJ-05 | OBJ-06 | OBJ-07 |
|--------|--------|--------|--------|--------|--------|--------|--------|
| IRQ-01 |        |        |   X    |        |        |        |        |
| IRQ-02 |   X    |   X    |        |        |        |        |        |
| IRQ-03 |   X    |        |        |        |        |   X    |        |
| IRQ-04 |        |        |        |   X    |   X    |        |        |
| IRQ-05 |        |        |        |        |   X    |        |        |
| IRQ-06 |        |        |        |        |        |        |   X    |
| RF-01  |   X    |        |        |        |        |        |        |
| RF-02  |   X    |   X    |        |        |        |        |        |
| RF-03  |   X    |   X    |        |        |        |        |        |
| RF-04  |   X    |   X    |        |        |        |        |        |
| RF-05  |   X    |        |        |        |        |        |        |
| RF-06  |        |        |   X    |        |        |        |        |
| RF-07  |        |        |   X    |        |        |        |        |
| RF-08  |        |        |   X    |        |        |        |        |
| RF-09  |   X    |   X    |        |        |        |        |        |
| RF-10  |        |   X    |        |        |   X    |        |        |
| RF-11  |        |        |        |   X    |        |        |        |
| RF-12  |        |        |        |   X    |        |        |        |
| RF-13  |        |   X    |        |        |        |        |        |
| RF-14  |        |   X    |        |        |        |        |        |
| RF-15  |        |        |        |        |        |   X    |        |
| RF-16  |        |        |        |        |   X    |        |        |
| RF-17  |        |        |        |        |   X    |        |        |
| RF-18  |        |        |        |        |        |   X    |        |
| RF-19  |        |        |        |        |        |   X    |        |
| RF-20  |        |        |        |        |        |   X    |        |
| RF-21  |        |        |        |        |        |        |   X    |
| RF-22  |        |        |        |        |        |        |   X    |
| RF-23  |        |        |        |        |        |        |   X    |
| RNF-01 |   X    |        |        |        |        |   X    |        |
| RNF-02 |        |        |        |        |        |   X    |        |
| RNF-03 |        |        |        |        |        |   X    |        |
| RNF-04 |        |        |   X    |        |   X    |        |        |

---

## Glosario de Terminos

| Termino      | Categoria   | Comentarios                                                                 |
|-------------|-------------|-----------------------------------------------------------------------------|
| Cancha       | Dominio     | Espacio deportivo reservable (futbol 5 o padel).                            |
| Complejo     | Dominio     | Establecimiento deportivo que agrupa una o mas canchas bajo un mismo owner. |
| Reserva      | Dominio     | Registro de un horario reservado por un usuario en una cancha.              |
| MercadoPago  | Tecnico     | Pasarela de pago utilizada para procesar cobros y reembolsos.               |
| Livewire     | Tecnico     | Framework de Laravel para componentes reactivos sin JavaScript custom.      |
| Flux UI      | Tecnico     | Libreria de componentes UI para Livewire.                                   |
| Superadmin   | Rol         | Administrador global con acceso total al sistema.                           |
| Owner        | Rol         | Dueno de uno o mas complejos deportivos.                                    |
| Staff        | Rol         | Empleado asignado a un complejo que confirma asistencia y gestiona reservas del dia. |
| User         | Rol         | Usuario final que reserva y paga canchas.                                   |
| Soft Delete  | Tecnico     | Patron de borrado logico que marca registros como eliminados sin borrarlos fisicamente. |
| Observer     | Tecnico     | Patron de Laravel que escucha eventos del modelo para ejecutar logica automatica. |
