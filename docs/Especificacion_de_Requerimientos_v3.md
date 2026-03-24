# Proyecto AposPlay

## Documento de Requisitos del Sistema

**Versión 3.0**
**Fecha:** 21/03/2026

**Realizado por:** Ivasiuta, Mariano - Martinez, Alejandro
**Realizado para:** Cliente

---

## Lista de Cambios

| Nro | Fecha      | Descripción                                                                                                                                                                                  | Autor  |
|-----|------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|--------|
| 0   | 15/06/2025 | Versión 1.0                                                                                                                                                                                  | Equipo |
| 1   | 14/03/2026 | Actualización v2.0 — sincronización con código implementado. Se corrigieron UCs parciales, se marcaron UCs pendientes, se agregaron UCs 21-23 y módulo de complejos.                         | Equipo |
| 2   | 21/03/2026 | Actualización v3.0 — Renumeración completa de UCs según lista de Requisitos Funcionales actualizada. Se agregaron UCs 23-30F. Se reescribieron todos los CUs extendidos con detalle de campos, botones y secciones coherentes con la interfaz implementada. | Equipo |
| 3   | 24/03/2026 | Actualización v4.0 — Se agrega OBJ-08, IRQ-08 y el módulo de Torneos (UC-32 a UC-35D). Se actualiza Matriz de Rastreabilidad y Glosario. | Equipo |

---

## Índice

- [Presentación General](#presentación-general)
- [Participantes del Proyecto](#participantes-del-proyecto)
- [Objetivos del sistema](#objetivos-del-sistema)
- [Requisitos del Sistema](#requisitos-del-sistema)
  - [Requisitos de Información](#requisitos-de-información)
  - [Requisitos Funcionales](#requisitos-funcionales)
  - [Diagrama de Casos de Usos](#diagrama-de-casos-de-usos)
  - [Definición de Actores](#definición-de-actores)
  - [Caso de Usos del Sistema](#caso-de-usos-del-sistema)
  - [Requisitos No funcionales](#requisitos-no-funcionales)
- [Matriz de Rastreabilidad Objetivo/Requisitos](#matriz-de-rastreabilidad-objetivorequisitos)
- [Glosario de Términos](#glosario-de-términos)

---

## Presentación General

AposPlay es una aplicación web basada en Laravel 12 que permite a clubes y complejos deportivos gestionar de manera integral la reserva de canchas de fútbol 5 y pádel en la ciudad de Apóstoles, Misiones. El sistema pretende reemplazar las planillas telefónicas y hojas de cálculo actuales por un flujo de trabajo centralizado que:

- Ofrece a los usuarios finales la posibilidad de registrarse, consultar la disponibilidad en tiempo real y pagar sus reservas en línea.
- Brinda a los administradores herramientas de administración de canchas, control de agenda, reportes y programas de fidelización.
- Integra pasarelas de pago (MercadoPago) y canales de notificación (correo) para asegurar la cobertura completa del ciclo de vida de la reserva.
- Implementa un sistema de roles (superadmin, owner, staff, usuario) con paneles diferenciados por rol y gestión de complejos deportivos.

---

## Participantes del Proyecto

**Desarrolladores:**
- Ivasiuta, Mariano
- Martinez, Alejandro

**Cliente:**
- Dueños de canchas de fútbol 5 y pádel, personal de recepción (Staff), usuarios que reservan.

---

## Objetivos del sistema

| ID     | Nombre                              | Descripción                                                                                       | Estabilidad | Comentarios |
|--------|-------------------------------------|---------------------------------------------------------------------------------------------------|-------------|-------------|
| OBJ-01 | Gestionar reservas en línea         | Permitir la creación, modificación y cancelación de reservas de canchas en tiempo real.            | Alta        | Ninguno     |
| OBJ-02 | Maximizar la ocupación              | Optimizar los horarios libres y reducir tiempos muertos entre reservas mediante notificaciones y pago anticipado. | Media       | Ninguno     |
| OBJ-03 | Facilitar la administración de canchas | Habilitar ABM de canchas, horarios de atención y bloqueos especiales.                           | Alta        | Ninguno     |
| OBJ-04 | Gestionar pagos y reembolsos        | Procesar cobros, señas y devoluciones de forma segura.                                            | Alta        | Ninguno     |
| OBJ-05 | Proveer métricas y reportes         | Ofrecer estadísticas de ocupación e ingresos para la toma de decisiones.                          | Media       | Ninguno     |
| OBJ-06 | Fidelizar a los clientes            | Otorgar puntos y promociones canjeables por tiempo de juego.                                      | Media       | Ninguno     |
| OBJ-07 | Gestionar roles y permisos          | Permitir la administración de roles (superadmin, owner, staff, user) con acceso diferenciado por panel. | Alta        | Ninguno     |
| OBJ-08 | Gestionar torneos deportivos        | Permitir a los owners crear y administrar torneos. A los usuarios inscribir equipos, pagar y seguir resultados en tiempo real. | Media       | Ninguno     |

---

## Requisitos del Sistema

### Requisitos de Información

| ID     | Nombre                            | Objetivos asociados                                    | Requisitos asociados                                                | Descripción                                                                                                                                    | Datos específicos                                                                                                          | Estabilidad |
|--------|-----------------------------------|--------------------------------------------------------|---------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------|-------------|
| IRQ-01 | Información de canchas            | OBJ-03                                                 | UC-30, UC-30A, UC-30B, UC-30C, UC-30D, UC-30E, UC-30F, UC-25       | El sistema deberá almacenar la información correspondiente a las canchas.                                                                      | Nombre, tipo (fútbol/pádel), precio por hora, estado (activa/inactiva), horarios por día, bloqueos especiales, complejo.   | Alta        |
| IRQ-02 | Información de reservas           | OBJ-01, OBJ-02                                        | UC-03, UC-04, UC-05, UC-06, UC-07, UC-17                           | El sistema deberá almacenar la información correspondiente a las reservas.                                                                     | Fecha, hora inicio/fin, cancha, usuario, estado, pago asociado, puntos canjeados, cupón aplicado, precio final.            | Alta        |
| IRQ-03 | Información de usuarios           | OBJ-01, OBJ-06                                        | UC-01, UC-01A, UC-01B, UC-01C, UC-09, UC-08, UC-11                 | El sistema deberá almacenar la información correspondiente a los usuarios.                                                                     | Perfil, datos de contacto, preferencias de notificación, saldo de puntos, rol.                                             | Media       |
| IRQ-04 | Información de pagos y reembolsos | OBJ-04, OBJ-05                                        | UC-07, UC-16, UC-23, UC-24, UC-26                                  | El sistema deberá almacenar la información correspondiente a pagos y reembolsos.                                                               | Monto, moneda, medio de pago, estado, referencia pasarela, fecha.                                                          | Alta        |
| IRQ-05 | Información de métricas de uso    | OBJ-05                                                 | UC-25, UC-26                                                        | El sistema deberá almacenar la información correspondiente a métricas de uso.                                                                  | Utilización por cancha, ingresos agrupados, ranking de clientes.                                                           | Baja        |
| IRQ-06 | Información de complejos          | OBJ-07                                                 | UC-18, UC-19, UC-20, UC-21, UC-22, UC-28, UC-29, UC-29A, UC-29B    | El sistema deberá almacenar la información correspondiente a los complejos deportivos.                                                         | Nombre, dirección, owner (FK users), estado activo/inactivo, canchas asociadas, staff asignado.                            | Alta        |
| IRQ-07 | Información de auditoría          | OBJ-03, OBJ-05                                        | UC-31                                                               | El sistema deberá registrar un log de auditoría por cada acción relevante realizada por los usuarios sobre el sistema.                          | Usuario, fecha y hora, tipo de acción (creación, edición, eliminación, login, pago, cancelación, reembolso), elemento afectado (cancha, reserva, complejo, etc.), detalle del cambio, dirección IP. | Alta        |
| IRQ-08 | Información de torneos            | OBJ-08                                                 | UC-32, UC-32A, UC-32B, UC-32C, UC-32D, UC-32E, UC-33, UC-34, UC-34A, UC-34B, UC-35 | El sistema deberá almacenar la información correspondiente a torneos, equipos, partidos y estadísticas de jugadores. | Torneo: nombre, deporte, formato (liga/eliminación directa), estado, precio de inscripción, cupo máximo, cancha, owner, fechas. Equipo: nombre, capitán, estado de pago. Integrante: user, número de camiseta, posición. Partido: ronda, equipos, score, estado, fecha. Estadística: goles, asistencias, tarjetas amarillas/rojas, minutos jugados por partido y jugador. | Alta        |

---

### Requisitos Funcionales

1. UC-01 — Gestionar Usuario
2. UC-01A — Registrar / Iniciar sesión
3. UC-01B — Recuperar contraseña
4. UC-01C — Gestionar perfil
5. UC-02 — Ver disponibilidad de canchas
6. UC-03 — Reservar cancha
7. UC-04 — Cancelar reserva
8. UC-05 — Ver mis reservas
9. UC-06 — Modificar reserva
10. UC-07 — Pagar reserva online
11. UC-08 — Canjear puntos
12. UC-09 — Acumular puntos
13. UC-10 — Aplicar cupón en reserva
14. UC-11 — Ver historial de puntos
15. UC-12 — Notificar cancelación
16. UC-13 — Enviar recordatorio de juego
17. UC-14 — Notificación asignación cupón
18. UC-15 — Explorar canchas (público)
19. UC-16 — Reembolsar pago
20. UC-17 — Confirmar asistencia
21. UC-18 — Crear cuenta Owner
22. UC-19 — Ver todos los Owners
23. UC-20 — Desactivar Owner
24. UC-21 — Reactivar Owner
25. UC-22 — Ver Panel adaptado por Rol
26. UC-23 — Procesar pago con MercadoPago
27. UC-24 — Pago fallido
28. UC-25 — Ver reportes de ocupación
29. UC-26 — Exportar ingresos
30. UC-27 — Gestionar promociones
31. UC-27A — Crear cupones
32. UC-27B — Editar cupones
33. UC-27C — Deshabilitar cupones
34. UC-27D — Eliminar cupones
35. UC-27E — Ver cupones
36. UC-28 — Gestionar Staff
37. UC-28A — Crear cuenta Staff
38. UC-28B — Asignar Staff a complejo
39. UC-28C — Revocar acceso Staff
40. UC-29 — Gestionar complejos
41. UC-29A — Crear complejo
42. UC-29B — Editar complejo
43. UC-29C — Eliminar complejo
44. UC-29D — Ver complejos
45. UC-30 — Gestionar cancha
46. UC-30A — Ver canchas
47. UC-30B — Editar cancha
48. UC-30C — Crear cancha
49. UC-30D — Deshabilitar cancha
50. UC-30E — Eliminar cancha
51. UC-30F — Definir horarios
52. UC-31 — Ver auditoría
53. UC-32 — Gestionar torneos
54. UC-32A — Crear torneo
55. UC-32B — Editar torneo
56. UC-32C — Gestionar inscripciones de equipos
57. UC-32D — Generar fixture
58. UC-32E — Registrar resultado de partido
59. UC-33 — Ver listado de torneos
60. UC-34 — Inscribirse en torneo
61. UC-34A — Registrar equipo
62. UC-34B — Pagar inscripción de equipo
63. UC-34C — Dar de baja equipo del torneo
64. UC-35 — Ver detalle y estadísticas de torneo

---

### Diagrama de Casos de Usos

**Subsistemas del sistema AposPlay:**
- Gestión de usuarios y permisos
- Reservas
- Administración de canchas
- Pagos
- Notificaciones y Automatizaciones
- Fidelización y Promociones
- Auditoría
- Torneos

*(Ver imagen adjunta: AposPlay_Completo.png)*

---

### Definición de Actores

| ID     | Nombre           | Descripción                                                                                      | Comentarios    |
|--------|------------------|--------------------------------------------------------------------------------------------------|----------------|
| ACT-01 | Usuario          | Persona que reserva y paga una cancha. Rol: `user`.                                              | Ninguno        |
| ACT-02 | Superadmin       | Administrador global con acceso total al sistema. Crea cuentas de owner. Rol: `superadmin`.      | Ninguno        |
| ACT-03 | Owner            | Dueño de complejos deportivos. Gestiona sus propios complejos y staff. Rol: `owner`.             | Nuevo en v2.   |
| ACT-04 | Staff            | Empleado asignado a un complejo que confirma asistencia y gestiona reservas del día. Rol: `staff`. | Ninguno        |
| ACT-05 | Sistema de pagos | Pasarela externa (MercadoPago).                                                                  | Ninguno        |
| ACT-06 | Job Scheduler    | Componente que ejecuta tareas programadas (cron job de Laravel).                                 | Ninguno        |

---

### Caso de Usos del Sistema

---

#### UC-01 — Gestionar Usuario

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El sistema permite que un usuario cree una cuenta nueva, inicie sesión con credenciales existentes, recupere su contraseña o gestione su perfil. Este caso de uso agrupa los sub-casos UC-01A, UC-01B y UC-01C. |
| **Precondición** | Ninguna (para registro). El usuario debe estar autenticado para gestión de perfil. |
| **Secuencia normal** | Ver sub-casos UC-01A, UC-01B y UC-01C. |
| **Postcondición** | El usuario accede al sistema con su sesión activa, o su perfil queda actualizado. |
| **Excepciones** | Ver sub-casos. |
| **Rendimiento** | Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-01A — Registrar / Iniciar sesión

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El sistema permite que un usuario cree una cuenta nueva o inicie sesión con credenciales existentes. |
| **Precondición** | El usuario no está autenticado. |
| **Secuencia normal** | **Paso 1:** El usuario accede a la pantalla de inicio y selecciona "Registrate" o "Iniciar sesion". **Paso 2 (Registro):** El sistema muestra la pantalla "Crear cuenta" con el subtítulo "Completa tus datos para registrarte". Se presentan los campos: "Nombre" (placeholder: "Nombre completo"), "Email" (placeholder: "email@example.com"), "Contraseña" (placeholder: "Contraseña") y "Confirmar contraseña" (placeholder: "Confirmar contraseña"). **Paso 2 (Login):** El sistema muestra la pantalla "Iniciar sesion" con el subtítulo "Ingresa tu email y contraseña para acceder". Se presentan los campos: "Email" (placeholder: "email@example.com") y "Contraseña" (placeholder: "Contraseña"), junto con el checkbox "Recordarme". **Paso 3:** El usuario completa los campos del formulario. **Paso 4:** El usuario presiona el botón "Crear cuenta" (registro) o "Iniciar sesion" (login). **Paso 5:** El sistema valida la información ingresada e inicia la sesión del usuario. |
| **Postcondición** | El usuario accede al Home del sistema. |
| **Excepciones** | **Paso 5:** Si el usuario ingresó credenciales inválidas, el sistema muestra un mensaje de error debajo de los campos correspondientes y vuelve a mostrar el formulario. Si la cuenta está desactivada, el sistema muestra el mensaje "Tu cuenta ha sido desactivada. Contacta al administrador." |
| **Rendimiento** | Paso 5: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | La pantalla de login incluye el enlace "Olvidaste tu contraseña?" que lleva a UC-01B. La pantalla de registro incluye el enlace "Ya tenes una cuenta?" → "Iniciar sesion". |

---

#### UC-01B — Recuperar contraseña

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El sistema permite que un usuario recupere su contraseña mediante un enlace enviado por email. |
| **Precondición** | El usuario no está autenticado. Tiene una cuenta registrada. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Olvidaste tu contraseña?" en la pantalla de "Iniciar sesion". **Paso 2:** El sistema muestra la pantalla "Recuperar contraseña" con el subtítulo "Ingresa tu email para recibir un enlace de recuperacion". Se presenta el campo: "Email" (placeholder: "email@example.com"). **Paso 3:** El usuario ingresa su email y presiona el botón "Enviar enlace de recuperacion". **Paso 4:** El sistema envía un email con un enlace de restablecimiento. Se muestra el mensaje "A reset link will be sent if the account exists." **Paso 5:** El usuario accede al enlace del email. El sistema muestra la pantalla "Restablecer contraseña" con el subtítulo "Ingresa tu nueva contraseña". Se presentan los campos: "Email" (precargado), "Contraseña" (placeholder: "Nueva contraseña") y "Confirmar contraseña" (placeholder: "Confirmar contraseña"). **Paso 6:** El usuario completa los campos y presiona el botón "Restablecer contraseña". **Paso 7:** El sistema actualiza la contraseña y redirige a la pantalla de login. |
| **Postcondición** | La contraseña del usuario queda actualizada. |
| **Excepciones** | **Paso 4:** Si el email no existe en el sistema, se muestra el mismo mensaje genérico sin revelar la existencia de cuentas. |
| **Rendimiento** | Paso 4: Menos de 2 segundos. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | La pantalla incluye el enlace "O volver a" → "iniciar sesion". |

---

#### UC-01C — Gestionar perfil

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El usuario puede actualizar su información de perfil, cambiar su contraseña y eliminar su cuenta. |
| **Precondición** | El usuario está autenticado. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Settings" en el menú desplegable de usuario (esquina superior derecha). **Paso 2:** El sistema muestra la sección "Profile" con el subtítulo "Update your name and email address". Se presentan los campos: "Name" y "Email". **Paso 3:** El usuario modifica los campos deseados y presiona el botón "Save". **Paso 4:** El sistema valida y actualiza la información. Se muestra el mensaje "Saved." **Paso 5 (Cambio de contraseña — opcional):** El usuario accede a la sección "Update password" con el subtítulo "Ensure your account is using a long, random password to stay secure". Se presentan los campos: "Current password", "New password" y "Confirm Password". El usuario completa los campos y presiona "Save". **Paso 6 (Eliminar cuenta — opcional):** El usuario accede a la sección "Delete account" con el subtítulo "Delete your account and all of its resources". Al presionar "Delete account", el sistema muestra un modal "Are you sure you want to delete your account?" que solicita el campo "Password". El usuario ingresa su contraseña y presiona "Delete account" para confirmar, o "Cancel" para cancelar. |
| **Postcondición** | Los datos del perfil quedan actualizados, la contraseña cambiada, o la cuenta eliminada según la acción realizada. |
| **Excepciones** | **Paso 4:** Si el email ya está en uso por otro usuario, el sistema muestra un error de validación debajo del campo. **Paso 5:** Si la contraseña actual es incorrecta, el sistema muestra un error de validación. |
| **Rendimiento** | Paso 4: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | La sección "Appearance" permite cambiar el tema visual con opciones "Light", "Dark" y "System". |

---

#### UC-02 — Ver disponibilidad de canchas

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea, OBJ-02 Maximizar la ocupación |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | El sistema muestra una grilla con los intervalos libres y ocupados para cada cancha en los próximos 7 días. |
| **Precondición** | El usuario está autenticado. Existe al menos una cancha activa con horarios definidos. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Canchas" en el menú lateral de navegación. **Paso 2:** El sistema muestra la sección "Filtros" con los campos: "Fecha" (selector de fecha, por defecto la fecha actual) y "Tipo de cancha" (opciones: "Todas", "Futbol", "Padel"). **Paso 3:** El usuario selecciona la fecha y tipo de cancha deseados. **Paso 4:** El sistema consulta la disponibilidad y muestra, para cada cancha, la sección "Horarios Disponibles:" con una grilla de botones por hora. Cada slot muestra una etiqueta de estado: "Disponible" (verde, clickeable), "Reservado" (gris, deshabilitado) o "No disponible" (deshabilitado). Para cada cancha se muestra: nombre, dirección y precio por hora. **Paso 5:** El usuario puede cambiar la fecha para ver otros días (hasta 7 días adelante). |
| **Postcondición** | El usuario visualiza la disponibilidad de canchas para la fecha seleccionada. |
| **Excepciones** | **Paso 4:** Si no hay canchas disponibles, el sistema muestra el mensaje "No hay canchas disponibles para la fecha seleccionada." con un botón "Restablecer filtros". |
| **Rendimiento** | Paso 4: Menos de 2 segundos. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-03 — Reservar cancha

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea, OBJ-02 Maximizar la ocupación |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al usuario generar una reserva de una cancha seleccionando fecha, hora y duración. |
| **Precondición** | El usuario está autenticado. La disponibilidad de la cancha está confirmada (UC-02). |
| **Secuencia normal** | **Paso 1:** El usuario hace clic en un slot con estado "Disponible" en la grilla de disponibilidad. **Paso 2:** El sistema abre el modal "Confirmar Reserva" con el mensaje "Por favor verifica los detalles de tu reserva:". Se muestra: nombre de la cancha, fecha, hora seleccionada y el campo "Duracion (horas)" (opciones: "1 hora", "2 horas", "3 horas"). Se muestra "Precio por hora:" con el monto correspondiente. **Paso 3:** El usuario selecciona la duración deseada. **Paso 4 (opcional — Cupón):** Si el usuario tiene un cupón, aparece la sección "¿Tenés un cupón de descuento?" con un campo de texto (placeholder: "Ej: APOS-ABC123") y el botón "Aplicar" (ver UC-10). **Paso 5 (opcional — Puntos):** Si el usuario tiene puntos suficientes, aparece el checkbox "Usar [X] puntos ([Y]% de descuento)" con el texto "Saldo disponible: [X] puntos" (ver UC-08). Si no tiene puntos suficientes, aparece el mensaje "Necesitás [X] puntos para obtener [Y]% de descuento (tenés [Z])". **Paso 6:** El usuario presiona el botón "Confirmar Reserva". **Paso 7:** El sistema verifica la disponibilidad del horario seleccionado y que no existan bloqueos activos. **Paso 8:** El sistema crea la reserva con estado "Pendiente de Pago" y bloquea la disponibilidad de la cancha en ese horario. **Paso 9:** El sistema muestra el mensaje "¡Éxito!" y redirige a "Mis Reservas". |
| **Postcondición** | Reserva registrada con estado "Pendiente de Pago". Disponibilidad de cancha bloqueada en esa fecha y horario. |
| **Excepciones** | **Paso 7:** Si no es posible reservar la cantidad de horas ingresadas en dicho horario (ocupado o bloqueado), se muestra un mensaje de error y se le pide reintentar. El usuario puede presionar "Cancelar" en el modal para volver a la grilla. |
| **Rendimiento** | Paso 8: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Si el usuario activó la opción de usar puntos, los puntos se descuentan en la misma operación de creación de reserva. |

---

#### UC-04 — Cancelar reserva

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea, OBJ-02 Maximizar la ocupación |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al usuario cancelar una reserva existente con al menos 24 horas de anticipación. |
| **Precondición** | El usuario está autenticado. Tiene una reserva en estado "Pendiente de Pago" o "Confirmada". Faltan al menos 24 horas para la hora reservada. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Mis Reservas" en el menú lateral de navegación. **Paso 2:** El sistema muestra la pantalla "Mis Reservas" con el listado de reservas del usuario. **Paso 3:** El usuario ubica la reserva que desea cancelar. El botón "Cancelar" aparece habilitado si faltan más de 24 horas. **Paso 4:** El usuario presiona el botón "Cancelar". **Paso 5:** El sistema muestra un cuadro de diálogo con el mensaje "¿Estás seguro que deseas cancelar esta reserva?". **Paso 6:** El usuario confirma la cancelación. **Paso 7:** El sistema modifica el estado de la reserva a "Cancelada". Si la reserva tenía puntos asignados, el sistema los revierte automáticamente. El sistema envía una notificación al staff del complejo (UC-12). **Paso 8:** El sistema muestra el mensaje "¡Éxito!" y actualiza el listado de reservas. |
| **Postcondición** | Reserva cancelada. Disponibilidad de cancha liberada. Puntos revertidos si corresponde. Staff notificado. |
| **Excepciones** | **Paso 3:** Si faltan menos de 24 horas, en lugar del botón "Cancelar" aparece el texto "No se puede cancelar (menos de 24hs)" y la acción no está disponible. |
| **Rendimiento** | Paso 7: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-05 — Ver mis reservas

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | El sistema permite que un usuario consulte el historial de reservas realizadas. |
| **Precondición** | El usuario está autenticado. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Mis Reservas" en el menú lateral de navegación. **Paso 2:** El sistema muestra la pantalla "Mis Reservas". Para cada reserva se muestra: fecha (formato: "lunes 5 de marzo, 2026"), hora de inicio y fin, nombre de la cancha, precio total (formato: "$X"), estado con etiqueta de color ("Pendiente", "Pendiente de Pago", "Confirmada" o "Cancelada"). **Paso 3:** Cada reserva muestra botones de acción según su estado: "Pagar Reserva" (si está en "Pendiente de Pago"), "Modificar" (si faltan al menos 4 horas), "Cancelar" (si faltan al menos 24 horas). |
| **Postcondición** | El usuario visualiza sus reservas con toda la información correspondiente. |
| **Excepciones** | **Paso 2:** Si el usuario no tiene reservas, se muestra el mensaje "No tienes reservas registradas." |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Los mensajes flash de la pantalla usan los formatos: "¡Éxito! [mensaje]", "Error: [mensaje]", "Atención: [mensaje]". |

---

#### UC-06 — Modificar reserva

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea, OBJ-02 Maximizar la ocupación |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al usuario reprogramar una reserva existente (cambiar fecha, hora o duración) con al menos 4 horas de anticipación. |
| **Precondición** | El usuario está autenticado. Tiene una reserva en estado "Confirmada" o "Pendiente de Pago". Faltan al menos 4 horas para la hora reservada. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis Reservas", el usuario presiona el botón "Modificar" en la reserva correspondiente. **Paso 2:** El sistema verifica que faltan al menos 4 horas para la reserva. **Paso 3:** El sistema abre el modal "Reprogramar Reserva" con los campos: "Nueva fecha" (selector de fecha), "Nuevo horario" (selector desplegable, inicialmente vacío) y "Duracion (horas)" (selector numérico). **Paso 4:** El usuario selecciona una nueva fecha. El sistema carga automáticamente los slots disponibles en el campo "Nuevo horario". **Paso 5:** El usuario selecciona el nuevo horario y la duración. **Paso 6:** El usuario presiona el botón "Confirmar Cambio". **Paso 7:** El sistema valida que el nuevo horario esté disponible y que no existan bloqueos activos. **Paso 8:** El sistema actualiza la reserva con la nueva fecha y hora. El slot anterior se libera y el nuevo se bloquea. **Paso 9:** El sistema muestra el mensaje "¡Éxito!" y cierra el modal. |
| **Postcondición** | La reserva tiene la nueva fecha y hora confirmada. |
| **Excepciones** | **Paso 2:** Si faltan menos de 4 horas, el botón "Modificar" no aparece en la reserva. **Paso 4:** Si no hay horarios disponibles para la fecha seleccionada, se muestra el mensaje "No hay horarios disponibles para esta fecha." **Paso 7:** Si el horario seleccionado ya no está disponible, se muestra un mensaje de error y se solicita seleccionar otro horario. El usuario puede presionar "Cancelar" para cerrar el modal sin cambios. |
| **Rendimiento** | Paso 8: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-07 — Pagar reserva online

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-04 Gestionar pagos y reembolsos |
| **Requisitos asociados** | IRQ-04 Información de pagos y reembolsos |
| **Descripción** | Permite al usuario iniciar el pago de una reserva pendiente a través de MercadoPago. |
| **Precondición** | La reserva está en estado "Pendiente de Pago". |
| **Secuencia normal** | **Paso 1:** El usuario accede a "Mis Reservas" desde el menú lateral. **Paso 2:** El sistema muestra el listado de reservas. Las reservas con estado "Pendiente de Pago" muestran el botón "Pagar Reserva". **Paso 3:** El usuario presiona el botón "Pagar Reserva" en la reserva correspondiente. **Paso 4:** El sistema redirige al usuario a la pasarela de MercadoPago (ver UC-23). **Paso 5:** El usuario completa el pago en MercadoPago. **Paso 6:** Tras el pago exitoso, el sistema actualiza el estado de la reserva a "Confirmada" y redirige a "Mis Reservas" con el mensaje "¡Éxito!". El sistema asigna automáticamente puntos de fidelidad (UC-09). |
| **Postcondición** | La reserva tiene estado "Confirmada". Puntos de fidelidad asignados al usuario. |
| **Excepciones** | **Paso 2:** Si no hay reservas pendientes de pago, el botón "Pagar Reserva" no aparece. **Paso 5:** Si el pago falla en MercadoPago, ver UC-24. |
| **Rendimiento** | Paso 4: Menos de 1 segundo (redirección). |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-08 — Canjear puntos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El usuario puede aplicar puntos acumulados como descuento al momento de realizar una nueva reserva. Si tiene al menos 50 puntos, obtiene un 30% de descuento sobre el precio de la reserva. |
| **Precondición** | El usuario está autenticado. Tiene un saldo de puntos suficiente (≥ 50 puntos). Está en el flujo de reserva (UC-03). |
| **Secuencia normal** | **Paso 1:** El usuario inicia una nueva reserva (UC-03) y el sistema abre el modal "Confirmar Reserva". **Paso 2:** El sistema muestra debajo del precio el checkbox "Usar [X] puntos ([Y]% de descuento)" junto con el texto "Saldo disponible: [X] puntos". **Paso 3:** El usuario activa el checkbox de usar puntos. **Paso 4:** El sistema recalcula el precio y muestra el nuevo total con el descuento del 30% aplicado. **Paso 5:** El usuario presiona "Confirmar Reserva". **Paso 6:** El sistema descuenta los puntos del saldo del usuario, registra los puntos canjeados en la reserva, y calcula el precio final con el descuento aplicado. |
| **Postcondición** | Reserva creada con descuento aplicado. Puntos descontados del saldo del usuario. |
| **Excepciones** | **Paso 2:** Si el usuario no tiene puntos suficientes (< 50), en lugar del checkbox se muestra el mensaje "Necesitás [X] puntos para obtener [Y]% de descuento (tenés [Z])" y la opción está deshabilitada. La reserva puede continuar sin descuento. |
| **Rendimiento** | Paso 6: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes por usuario. |
| **Estabilidad** | Media |
| **Comentarios** | Los valores de 50 puntos requeridos y 30% de descuento son configurables por el administrador. |

---

#### UC-09 — Acumular puntos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El sistema asigna puntos de fidelidad automáticamente al usuario por cada reserva pagada. Se otorgan 5 puntos por reserva pagada. |
| **Precondición** | El usuario debe haber pagado una reserva correctamente (estado cambia a "Confirmada" vía UC-07). |
| **Secuencia normal** | **Paso 1:** El usuario realiza y paga una reserva (UC-07). **Paso 2:** El sistema detecta que el estado de la reserva cambió a "Confirmada". **Paso 3:** El sistema asigna automáticamente 5 puntos al usuario y registra una transacción de tipo "Ganados" vinculada a la reserva, con la descripción correspondiente. |
| **Postcondición** | El usuario acumula puntos en su perfil. La transacción queda visible en "Mis Puntos" (UC-11). |
| **Excepciones** | **Paso 2:** Si la reserva ya tenía puntos asignados, el sistema no crea un registro duplicado. **Paso 2 (alternativo):** Si la reserva es cancelada o reembolsada posteriormente, los puntos se revierten automáticamente creando una transacción de tipo "Revertidos". |
| **Rendimiento** | Paso 3: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | El valor de 5 puntos por reserva es configurable por el administrador. |

---

#### UC-10 — Aplicar cupón en reserva

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al usuario aplicar un código de cupón de descuento al momento de confirmar una reserva. |
| **Precondición** | El usuario está autenticado. Está en el flujo de reserva (UC-03). Tiene un cupón válido asignado. |
| **Secuencia normal** | **Paso 1:** Durante el proceso de reserva (UC-03), en el modal "Confirmar Reserva" aparece la sección "¿Tenés un cupón de descuento?". **Paso 2:** El usuario ingresa el código del cupón en el campo de texto (placeholder: "Ej: APOS-ABC123"). **Paso 3:** El usuario presiona el botón "Aplicar". **Paso 4:** El sistema valida el cupón: verifica que el código exista, que esté activo, que no haya expirado, que no haya excedido su límite de usos, y que el usuario no lo haya usado previamente. **Paso 5:** El sistema calcula el descuento según el tipo de cupón: porcentaje (%) o monto fijo ($). **Paso 6:** El sistema muestra el nuevo precio con el descuento reflejado en el modal. **Paso 7:** Al presionar "Confirmar Reserva", el sistema registra el cupón aplicado y el monto de descuento en la reserva, e incrementa el contador de usos del cupón. |
| **Postcondición** | Reserva creada con cupón aplicado y descuento reflejado en el precio. |
| **Excepciones** | **Paso 4:** Si el cupón no es válido (código inexistente, expirado, agotado o ya usado por el usuario), el sistema muestra un mensaje de error debajo del campo. El usuario puede continuar la reserva sin cupón presionando "Confirmar Reserva" directamente. |
| **Rendimiento** | Paso 4: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Media |
| **Comentarios** | Ninguno. |

---

#### UC-11 — Ver historial de puntos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El sistema muestra al usuario su saldo actual de puntos de fidelidad y el historial de las últimas transacciones. |
| **Precondición** | El usuario está autenticado. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Mis Puntos" en el menú lateral de navegación. **Paso 2:** El sistema muestra la pantalla "Mis Puntos" con dos secciones. **Sección superior:** una tarjeta con el texto "Saldo actual" y la cantidad de "puntos" del usuario. **Sección inferior:** la tabla "Últimas transacciones" con las columnas: "Tipo", "Puntos", "Descripción" y "Fecha". La columna "Tipo" muestra etiquetas de color según el tipo de transacción: "Ganados" (verde), "Canjeados" (azul), "Revertidos" (amarillo) o "Expirados" (rojo). **Paso 3:** El sistema muestra las últimas 10 transacciones ordenadas por fecha descendente. |
| **Postcondición** | El usuario visualiza su saldo y movimientos de puntos. |
| **Excepciones** | **Paso 2:** Si el usuario no tiene transacciones, se muestra saldo 0 y el mensaje "No tienes transacciones de puntos aún." |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-12 — Notificar cancelación

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-02 Maximizar la ocupación |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | El sistema envía automáticamente una notificación al staff del complejo cuando una reserva es cancelada, liberando el horario correspondiente. |
| **Precondición** | Una reserva cambia de estado a "Cancelada". |
| **Secuencia normal** | **Paso 1:** El sistema detecta que el estado de una reserva cambió a "Cancelada" (ya sea por acción del usuario en UC-04 o por reembolso en UC-16). **Paso 2:** El sistema identifica al staff asignado al complejo de la cancha de la reserva. **Paso 3:** El sistema envía una notificación de cancelación a cada miembro del staff del complejo informando los detalles de la reserva cancelada. |
| **Postcondición** | Staff del complejo notificado de la cancelación. Horario de la cancha liberado. |
| **Excepciones** | **Paso 2:** Si la cancha no tiene complejo asignado o el complejo no tiene staff, la notificación no se envía pero la cancelación se procesa normalmente. |
| **Rendimiento** | Paso 3: Menos de 30 segundos desde el evento. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-13 — Enviar recordatorio de juego

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-02 Maximizar la ocupación |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | El Job Scheduler del sistema envía recordatorios automáticos por email 24 horas y 1 hora antes de la hora de la reserva. |
| **Precondición** | Reserva en estado "Confirmada". |
| **Secuencia normal** | **Paso 1:** El cron del sistema identifica reservas cuyo horario se aproxima (24 horas y 1 hora antes). **Paso 2:** El sistema envía un email de recordatorio al usuario con los datos de la reserva: cancha, fecha, hora y dirección. |
| **Postcondición** | Usuario notificado del próximo juego. |
| **Excepciones** | Ninguna relevante. |
| **Rendimiento** | Paso 2: Menos de 1 segundo por notificación. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | **Estado: Pendiente de implementación.** |

---

#### UC-14 — Notificación asignación cupón

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | El sistema envía una notificación automática al usuario cuando se le asigna un cupón de descuento. |
| **Precondición** | Un administrador crea un cupón y lo asigna a uno o más usuarios (UC-27A). |
| **Secuencia normal** | **Paso 1:** El administrador crea un cupón y selecciona los clientes a los que aplica (UC-27A). **Paso 2:** El sistema registra la asignación del cupón a los usuarios seleccionados. **Paso 3:** El sistema envía una notificación por email a cada usuario informando que tiene un nuevo cupón disponible para su próxima reserva. |
| **Postcondición** | Los usuarios reciben la notificación del cupón asignado. |
| **Excepciones** | **Paso 3:** Si el envío de email falla, la asignación del cupón se mantiene. |
| **Rendimiento** | Paso 3: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Baja |
| **Comentarios** | **Estado: Parcialmente implementado.** La asignación de cupones a usuarios funciona. La notificación automática por email está pendiente. |

---

#### UC-15 — Explorar canchas (público)

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-01 Gestionar reservas en línea, OBJ-02 Maximizar la ocupación |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Permite a cualquier visitante (autenticado o no) explorar las canchas disponibles del sistema y ver su información general y horarios. |
| **Precondición** | Ninguna. Ruta pública. |
| **Secuencia normal** | **Paso 1:** El visitante accede a la sección "Canchas" desde el menú de navegación o directamente por URL. **Paso 2:** El sistema muestra la pantalla "Mis canchas" con un listado de tarjetas de las canchas activas. Cada tarjeta muestra: nombre de la cancha, tipo (etiqueta: "Futbol" o "Padel"), dirección (o "Sin dirección" si no tiene), "Jugadores: [número]". **Paso 3:** El visitante selecciona "Ver horarios" en una cancha. **Paso 4:** El sistema muestra los horarios de la cancha seleccionada organizados por día de la semana, turno mañana y turno tarde. |
| **Postcondición** | El visitante visualiza la información de las canchas y sus horarios. |
| **Excepciones** | **Paso 2:** Si no hay canchas activas, se muestra un mensaje informativo. |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-16 — Reembolsar pago

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-04 Gestionar pagos y reembolsos |
| **Requisitos asociados** | IRQ-04 Información de pagos y reembolsos |
| **Descripción** | Permite al staff o al administrador devolver el total o parcial del importe pagado de una reserva cancelada. El tipo de reembolso depende de la anticipación con la que se canceló. |
| **Precondición** | Reserva en estado "Cancelada" con pago registrado. El staff/owner/superadmin está autenticado. |
| **Secuencia normal** | **Paso 1:** El staff selecciona "Reservas del Dia" en el menú lateral (sección "Mi Complejo") o el admin selecciona "Reservas del Dia" (sección "Administracion"). **Paso 2:** El sistema muestra la pantalla "Reservas del Dia" con un selector de fecha y una tabla con las columnas: "Hora", "Cancha", "Usuario", "Estado", "Pago" y "Acciones". **Paso 3:** El staff/admin ubica la reserva cancelada que requiere reembolso. La columna "Acciones" muestra el botón "Reembolsar" si la reserva tiene pago registrado y es reembolsable, o el texto "No reembolsable" si no aplica. **Paso 4:** El staff/admin presiona el botón "Reembolsar". **Paso 5:** El sistema determina el tipo de reembolso y muestra el modal "Confirmar Reembolso" con los detalles: "Cancha:", "Usuario:", "Monto Original:" y "Monto a Reembolsar:". El mensaje indica: (a) "Se realizará un reembolso **TOTAL** del pago." si la reserva se canceló con al menos 8 horas de anticipación (contrato C-01). (b) "Se realizará un reembolso **PARCIAL (50%)** debido a que faltan menos de 8 horas." si la reserva se canceló con menos de 8 horas pero al menos 2 horas de anticipación (contrato C-02). **Paso 6:** El staff/admin presiona el botón "Confirmar Reembolso". **Paso 7:** El sistema procesa el reembolso a través de MercadoPago y actualiza el estado de pago de la reserva. Se muestra un mensaje de éxito. |
| **Postcondición** | Reembolso procesado. Estado de pago actualizado en la reserva. |
| **Excepciones** | **Paso 3:** Si la reserva se canceló con menos de 2 horas de anticipación, el botón "Reembolsar" no aparece y se muestra "No reembolsable". **Paso 7:** Si la API de MercadoPago falla, se muestra un mensaje de error. El usuario puede presionar "Cancelar" en el modal para cerrar sin procesar. |
| **Rendimiento** | Paso 7: Menos de 2 segundos. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

**Contrato C-01 — Reembolsar pago completo:**

| Campo | Detalle |
|-------|---------|
| **Descripción** | Permite al staff devolver el total del importe pagado. |
| **Precondición** | Reserva en estado "Cancelada" con al menos 8 horas de anticipación. |
| **Secuencia** | **Paso 1:** El sistema muestra en el modal el mensaje "Se realizará un reembolso **TOTAL** del pago." con el monto completo en "Monto a Reembolsar:". **Paso 2:** El staff presiona "Confirmar Reembolso". **Paso 3:** El sistema procesa el reembolso completo vía MercadoPago. |

**Contrato C-02 — Reembolsar pago parcial:**

| Campo | Detalle |
|-------|---------|
| **Descripción** | Permite al staff devolver un importe parcial del pago (50%). |
| **Precondición** | Reserva en estado "Cancelada" entre 8 y 2 horas de anticipación. |
| **Secuencia** | **Paso 1:** El sistema muestra en el modal el mensaje "Se realizará un reembolso **PARCIAL (50%)** debido a que faltan menos de 8 horas." con el monto parcial en "Monto a Reembolsar:". **Paso 2:** El staff presiona "Confirmar Reembolso". **Paso 3:** El sistema procesa el reembolso parcial vía MercadoPago. |

---

#### UC-17 — Confirmar asistencia

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-02 Maximizar la ocupación, OBJ-05 Proveer métricas |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al staff marcar la asistencia de los jugadores para una reserva del día. |
| **Precondición** | El usuario está autenticado como staff. La reserva está en estado "Confirmada". Es la hora de la reserva. |
| **Secuencia normal** | **Paso 1:** El staff selecciona "Reservas del Dia" en el menú lateral (sección "Mi Complejo"). **Paso 2:** El sistema muestra la pantalla "Reservas del Dia" con un selector de fecha y una tabla con las columnas: "Hora", "Cancha", "Usuario", "Estado", "Pago" y "Acciones". El staff solo ve las reservas de los complejos a los que está asignado. **Paso 3:** El staff ubica la reserva correspondiente. La columna "Acciones" muestra el botón "Confirmar" para las reservas en estado "Paid" (pagada). **Paso 4:** El staff presiona el botón "Confirmar". **Paso 5:** El sistema muestra un cuadro de diálogo con el mensaje "¿Confirmar asistencia de esta reserva?". **Paso 6:** El staff confirma la asistencia. **Paso 7:** El sistema actualiza el estado de la reserva a "Confirmed" y muestra un mensaje de éxito. |
| **Postcondición** | La reserva tiene estado "Confirmed". |
| **Excepciones** | **Paso 3:** Si la reserva no está en estado "Paid", el botón "Confirmar" no aparece. |
| **Rendimiento** | Paso 7: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-18 — Crear cuenta Owner

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios, IRQ-06 Información de complejos |
| **Descripción** | El superadmin puede crear cuentas de usuario con rol owner, opcionalmente asociando un complejo deportivo. |
| **Precondición** | El usuario está autenticado como superadmin. |
| **Secuencia normal** | **Paso 1:** El superadmin selecciona "Gestionar Owners" en el menú lateral (sección "Superadmin"). **Paso 2:** El sistema muestra la pantalla "Gestionar Owners" (UC-19). El superadmin presiona el botón "Crear Owner". **Paso 3:** El sistema muestra la pantalla "Crear Owner" con un enlace "Volver" al listado. Se presentan los campos: "Nombre", "Email" y "Password". **Paso 4:** Debajo aparece la sección "Complejo (opcional)" con los campos: "Nombre del complejo" y "Direccion del complejo". **Paso 5:** El superadmin completa los campos obligatorios (nombre, email, password) y opcionalmente los datos del complejo. **Paso 6:** El superadmin presiona el botón "Crear Owner". **Paso 7:** El sistema valida los datos y crea la cuenta con rol owner. Si se proporcionó información de complejo, también crea el complejo asociado al nuevo owner. **Paso 8:** El sistema redirige al listado de owners con un mensaje de éxito. |
| **Postcondición** | Cuenta de owner creada. Complejo asociado si se proporcionó. |
| **Excepciones** | **Paso 7:** Si el email ya existe, el sistema muestra un error de validación debajo del campo "Email". |
| **Rendimiento** | Paso 7: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-19 — Ver todos los Owners

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios, IRQ-06 Información de complejos |
| **Descripción** | El superadmin puede ver un listado de todos los owners registrados, con sus complejos asociados y estado. |
| **Precondición** | El usuario está autenticado como superadmin. |
| **Secuencia normal** | **Paso 1:** El superadmin selecciona "Gestionar Owners" en el menú lateral (sección "Superadmin"). **Paso 2:** El sistema muestra la pantalla "Gestionar Owners" con un campo de búsqueda (placeholder: "Buscar por nombre o email...") y el botón "Crear Owner". **Paso 3:** El sistema muestra una tabla con las columnas: "Nombre", "Email", "Complejos", "Estado" y "Acciones". La columna "Estado" muestra etiquetas: "Activo" (verde) o "Inactivo" (rojo). La columna "Complejos" muestra los nombres de los complejos asociados al owner. **Paso 4:** El superadmin puede buscar owners escribiendo en el campo de búsqueda. La tabla se filtra automáticamente por nombre o email. **Paso 5:** Desde la columna "Acciones", el superadmin puede presionar "Desactivar" (UC-20) o "Reactivar" (UC-21) según el estado actual del owner. |
| **Postcondición** | El superadmin visualiza el listado completo de owners. |
| **Excepciones** | **Paso 3:** Si no hay owners registrados, se muestra el mensaje "No hay owners registrados." |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-20 — Desactivar Owner

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El superadmin puede desactivar una cuenta de owner, bloqueando su acceso al sistema sin eliminar sus datos. |
| **Precondición** | El usuario está autenticado como superadmin. El owner está en estado "Activo". |
| **Secuencia normal** | **Paso 1:** Desde el listado de owners (UC-19), el superadmin presiona el botón "Desactivar" en la columna "Acciones" del owner correspondiente. **Paso 2:** El sistema muestra un cuadro de confirmación con el mensaje "¿Desactivar este owner? No podra acceder al sistema." **Paso 3:** El superadmin confirma la acción. **Paso 4:** El sistema desactiva la cuenta del owner. **Paso 5:** El listado se actualiza: el estado del owner cambia a "Inactivo" (rojo) y el botón de la columna "Acciones" cambia a "Reactivar". Se muestra un mensaje de éxito. |
| **Postcondición** | El owner queda desactivado. Al intentar iniciar sesión, verá el mensaje "Tu cuenta ha sido desactivada. Contacta al administrador." Sus datos y complejos se mantienen. |
| **Excepciones** | Ninguna relevante. |
| **Rendimiento** | Paso 4: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al año. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-21 — Reactivar Owner

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El superadmin puede reactivar una cuenta de owner previamente desactivada, restaurando su acceso al sistema. |
| **Precondición** | El usuario está autenticado como superadmin. El owner está en estado "Inactivo". |
| **Secuencia normal** | **Paso 1:** Desde el listado de owners (UC-19), el superadmin presiona el botón "Reactivar" en la columna "Acciones" del owner correspondiente. **Paso 2:** El sistema reactiva la cuenta del owner. **Paso 3:** El listado se actualiza: el estado del owner cambia a "Activo" (verde) y el botón de la columna "Acciones" cambia a "Desactivar". Se muestra un mensaje de éxito. |
| **Postcondición** | El owner queda reactivado y puede acceder nuevamente al sistema. |
| **Excepciones** | Ninguna relevante. |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al año. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-22 — Ver Panel adaptado por Rol

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | El sistema presenta paneles diferenciados en el menú lateral según el rol del usuario autenticado, con acceso restringido mediante middleware. |
| **Precondición** | El usuario está autenticado. |
| **Secuencia normal** | **Paso 1:** El usuario inicia sesión en el sistema. **Paso 2:** El sistema identifica el rol del usuario (superadmin, owner, staff o user). **Paso 3:** El sistema muestra el menú lateral de navegación con las secciones correspondientes al rol: **Superadmin:** Sección "Superadmin" con "Gestionar Owners" y "Todas las Reservas". Sección "Administracion" con "Reservas del Dia", "Cupones y Descuentos", "Reporte de Ocupacion", "Exportar Ingresos", "Promociones" y "Bloqueos de Horario". Sección "Mi Complejo" con "Complejos", "Mis Canchas" y "Staff". Sección de usuario con "Inicio", "Mis Reservas", "Mis Puntos" y "Canchas". **Owner:** Sección "Administracion" con "Reservas del Dia", "Cupones y Descuentos", "Reporte de Ocupacion", "Exportar Ingresos", "Promociones" y "Bloqueos de Horario". Sección "Mi Complejo" con "Complejos", "Mis Canchas" y "Staff". **Staff:** Sección "Mi Complejo" con "Reservas del Dia" y "Promociones". **User:** "Inicio", "Mis Reservas", "Mis Puntos" y "Canchas". **Paso 4:** El middleware de roles bloquea el acceso a rutas no autorizadas para el rol del usuario. |
| **Postcondición** | El usuario ve únicamente las funciones correspondientes a su rol en el menú lateral. |
| **Excepciones** | **Paso 4:** Si el usuario intenta acceder a una ruta no autorizada (por ejemplo, escribiendo directamente la URL), el sistema retorna un error HTTP 403 (Acceso denegado). |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Cada inicio de sesión. |
| **Estabilidad** | Alta |
| **Comentarios** | El menú lateral también muestra enlaces externos: "Repositorio" y "Documentación". El menú de usuario (esquina superior derecha) muestra "Settings" y "Log Out". |

---

#### UC-23 — Procesar pago con MercadoPago

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-04 Gestionar pagos y reembolsos |
| **Requisitos asociados** | IRQ-04 Información de pagos y reembolsos |
| **Descripción** | El sistema procesa el pago de una reserva a través de la pasarela MercadoPago, creando una preferencia de pago, redirigiendo al usuario al checkout externo y procesando el callback de éxito. |
| **Precondición** | Reserva en estado "Pendiente de Pago". El usuario inició el pago desde UC-07 presionando "Pagar Reserva". |
| **Secuencia normal** | **Paso 1:** El sistema crea una preferencia de pago en la API de MercadoPago con los datos de la reserva: título de la cancha, precio y cantidad. **Paso 2:** El sistema redirige al usuario al checkout de MercadoPago (página externa). **Paso 3:** El usuario completa el pago en la interfaz de MercadoPago (selecciona medio de pago, ingresa datos de tarjeta, etc.). **Paso 4:** MercadoPago procesa el pago y redirige al usuario de vuelta al sistema con los parámetros del resultado. **Paso 5:** El sistema recibe el callback de éxito, valida el identificador de pago, y actualiza la reserva: estado a "Confirmada", estado de pago a "paid", y registra el identificador de pago y el monto pagado. **Paso 6:** El sistema redirige a "Mis Reservas" con el mensaje "¡Éxito!". |
| **Postcondición** | Reserva actualizada a estado "Confirmada". El sistema asigna puntos automáticamente (UC-09). |
| **Excepciones** | **Paso 1:** Si no está configurado el token de acceso de MercadoPago, el sistema muestra un error de configuración. **Paso 3:** Si el usuario cancela o el pago es rechazado en MercadoPago, se redirige al callback de fallo (UC-24). |
| **Rendimiento** | Paso 1: Menos de 2 segundos (incluye llamada a API externa). |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-24 — Pago fallido

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-04 Gestionar pagos y reembolsos |
| **Requisitos asociados** | IRQ-04 Información de pagos y reembolsos |
| **Descripción** | El sistema maneja el escenario donde el pago en MercadoPago falla o es rechazado, informando al usuario. |
| **Precondición** | El usuario intentó pagar una reserva (UC-23) y el pago fue rechazado o cancelado en MercadoPago. |
| **Secuencia normal** | **Paso 1:** MercadoPago redirige al usuario de vuelta al sistema con el resultado de fallo. **Paso 2:** El sistema muestra un mensaje de error informando que el pago no pudo ser procesado. **Paso 3:** El sistema redirige a "Mis Reservas" con el mensaje "Error: [motivo]". |
| **Postcondición** | La reserva mantiene su estado "Pendiente de Pago". El usuario puede reintentar el pago presionando "Pagar Reserva" nuevamente. |
| **Excepciones** | Ninguna. |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Ocasional. |
| **Estabilidad** | Alta |
| **Comentarios** | También existe un callback para pagos en estado "pendiente" que muestra un mensaje de "Atención:" indicando que el pago está siendo procesado. |

---

#### UC-25 — Ver reportes de ocupación

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-05 Proveer métricas y reportes |
| **Requisitos asociados** | IRQ-05 Información de métricas de uso |
| **Descripción** | Permite al administrador/owner visualizar estadísticas sobre la utilización de las canchas. |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | **Paso 1:** El administrador selecciona "Reporte de Ocupacion" en el menú lateral (sección "Administracion"). **Paso 2:** El sistema muestra la pantalla "Reporte de Ocupación" con la sección "Filtros". Se presentan botones de acceso rápido: "Hoy", "Esta semana", "Este mes". Debajo aparecen los campos de filtro: "Desde" (selector de fecha), "Hasta" (selector de fecha), "Cancha" (desplegable con opciones: "Todas las canchas" y la lista de canchas) y "Agrupar por" (opciones: "Cancha", "Franja horaria", "Día de la semana", "Semana", "Mes"). **Paso 3:** El administrador selecciona los filtros deseados. **Paso 4:** El sistema muestra tarjetas de resumen: "Total reservas", "Ingresos totales" y "Período". Debajo muestra una tabla con las columnas dinámicas según el agrupamiento seleccionado (por ejemplo: "Cancha", "Reservas", "Ingresos", "Ocupación relativa"). |
| **Postcondición** | Se muestran estadísticas de ocupación de las canchas. |
| **Excepciones** | **Paso 4:** Si no hay datos para los filtros seleccionados, el sistema muestra el mensaje "No hay información disponible para los filtros seleccionados." |
| **Rendimiento** | Paso 4: Menos de 2 segundos. |
| **Frecuencia** | Varias veces por semana. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-26 — Exportar ingresos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-05 Proveer métricas y reportes |
| **Requisitos asociados** | IRQ-04 Información de pagos y reembolsos |
| **Descripción** | Permite al administrador/owner exportar en formato CSV o PDF los ingresos obtenidos en un período determinado. |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | **Paso 1:** El administrador selecciona "Exportar Ingresos" en el menú lateral (sección "Administracion"). **Paso 2:** El sistema muestra la pantalla "Exportar Ingresos" con la sección "Período". Se presentan dos modos de filtrado seleccionables con botones: "Por mes" y "Rango de fechas". **Paso 3 (Modo "Por mes"):** El sistema muestra los campos: "Mes" (desplegable: "Enero" a "Diciembre") y "Año" (desplegable). **Paso 3 (Modo "Rango de fechas"):** El sistema muestra los campos: "Desde" (selector de fecha) y "Hasta" (selector de fecha). **Paso 4:** El sistema muestra tarjetas de resumen: "Registros", "Ingresos brutos", "Reembolsos" e "Ingreso neto". **Paso 5:** Debajo aparece la sección "Formato de exportación" con el mensaje "Se exportarán **[X]** registros del período seleccionado." y dos botones: "Exportar CSV" y "Exportar PDF". **Paso 6:** El administrador presiona uno de los botones de exportación. El botón muestra el texto "Generando..." mientras se procesa. **Paso 7:** El sistema genera el archivo y permite su descarga. |
| **Postcondición** | Archivo de ingresos exportado correctamente. |
| **Excepciones** | **Paso 4:** Si no existen ingresos en el período seleccionado, se muestra el mensaje "No hay datos para el período seleccionado. Ajustá los filtros para exportar." y los botones de exportación no están disponibles. También se muestra una alerta: "No existen ingresos para el período seleccionado." |
| **Rendimiento** | Paso 7: Menos de 5 segundos. |
| **Frecuencia** | 1 vez por mes (mínimo). |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-27 — Gestionar promociones

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-03 Información de usuarios |
| **Descripción** | Permite al administrador/owner/staff crear, editar, activar/desactivar y eliminar reglas de promoción. Los tipos soportados son: Combo, Cupón y Puntos Extra. |
| **Precondición** | El usuario está autenticado como superadmin, owner o staff. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Promociones" en el menú lateral (sección "Administracion" o "Mi Complejo" según el rol). **Paso 2:** El sistema muestra la pantalla "Promociones" con un campo de búsqueda (placeholder: "Buscar por nombre...") y el botón "Crear promoción". **Paso 3:** El sistema muestra una tabla con las columnas: "Nombre", "Tipo", "Descuento", "Puntos Bonus", "Vigencia", "Estado" y "Acciones". La columna "Tipo" muestra etiquetas: "Combo", "Cupón" o "Puntos Extra". La columna "Estado" muestra: "Activa" (verde), "Programada" (azul), "Inactiva" (gris) o "Expirada" (rojo). **Paso 4:** La columna "Acciones" muestra los enlaces: "Editar", "Desactivar"/"Activar" (según el estado actual) y "Eliminar". **Paso 5 (Crear/Editar):** El usuario presiona "Crear promoción" o "Editar". El sistema muestra la pantalla "Crear Promoción" o "Editar Promoción" con un enlace "Volver al listado". Se presentan los campos: "Nombre" (placeholder: "Ej: Promo Verano 2x1"), "Tipo" (opciones: "Combo", "Cupón", "Puntos Extra"), "Valor del descuento ($)", "Puntos bonus" (solo visible si el tipo es "Puntos Extra", placeholder: "Ej: 10"), "Fecha de inicio" (selector de fecha), "Fecha de fin" (selector de fecha), "Condiciones (JSON, opcional)" (placeholder: '{"min_reservations": 3, "court_types": ["futbol"]}'). **Paso 6:** El usuario completa los campos y presiona "Crear promoción" o "Guardar cambios". **Paso 7:** El sistema valida que no exista una promoción activa del mismo tipo con fechas superpuestas. **Paso 8:** El sistema guarda la promoción y redirige al listado con un mensaje de éxito. **Paso 9 (Activar/Desactivar):** El usuario presiona "Desactivar" o "Activar" en la columna "Acciones". El sistema cambia el estado de la promoción y actualiza la tabla. **Paso 10 (Eliminar):** El usuario presiona "Eliminar". El sistema muestra el mensaje "¿Estás seguro de que querés eliminar esta promoción?". El usuario confirma y la promoción se elimina del listado. |
| **Postcondición** | Promoción creada/actualizada/eliminada según la acción realizada. |
| **Excepciones** | **Paso 7:** Si existe una promoción del mismo tipo con fechas superpuestas, el sistema muestra un mensaje de conflicto en la parte superior del formulario con los detalles de la promoción existente. El usuario puede presionar "Cancelar" para volver al listado sin guardar. |
| **Rendimiento** | Paso 7: Menos de 2 segundos. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Media |
| **Comentarios** | Ninguno. |

---

#### UC-27A — Crear cupones

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al administrador crear cupones de descuento con código único, tipo, valor, límite de usos y rango de validez, y asignarlos a clientes seleccionados. |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | **Paso 1:** El administrador selecciona "Cupones y Descuentos" en el menú lateral (sección "Administracion"). **Paso 2:** El sistema muestra la pantalla "Cupones y Descuentos" con un campo de búsqueda (placeholder: "Buscar por código o descripción...") y el botón "Crear cupón". **Paso 3:** El administrador presiona "Crear cupón". El sistema muestra el formulario "Nuevo Cupón" con los campos: "Tipo de descuento" (opciones: "Porcentaje (%)" o "Monto Fijo ($)"), "Valor del descuento" (placeholder: "Ej: 15" si es porcentaje o "Ej: 500" si es monto fijo), "Descripción del cupón" (placeholder: "Ej: Descuento por ser cliente frecuente"), "Válido desde" (selector de fecha), "Válido hasta (opcional)" (selector de fecha), "Máximo de usos (opcional)" (placeholder: "Ilimitado"). **Paso 4:** Debajo aparece la sección "Clientes que reciben el cupón" con el texto auxiliar "Seleccioná los clientes a los que aplica este cupón. Se les enviará una notificación por email." Se muestra un checkbox "Seleccionar todos ([cantidad])" y la lista de usuarios con checkboxes individuales. Debajo se muestra el contador "[X] cliente(s) seleccionado(s)". **Paso 5:** El administrador completa los campos, selecciona los clientes y presiona "Guardar". **Paso 6:** El sistema muestra el modal "Confirmar creación del cupón" con el mensaje "¿Estás seguro de que querés crear este cupón?" y los detalles: "Tipo:", "Valor:", "Descripción:", "Clientes:" y "Válido hasta:". Incluye la nota: "Se enviará una notificación por email a los [X] cliente(s) seleccionado(s) informándoles del cupón." **Paso 7:** El administrador presiona "Confirmar y Crear". **Paso 8:** El sistema crea el cupón con un código auto-generado, lo activa, y lo asigna a los clientes seleccionados. El código del cupón se genera automáticamente con formato "APOS-XXXXXX". **Paso 9:** El sistema redirige al listado de cupones con un mensaje de éxito. |
| **Postcondición** | Cupón creado y activo. Asignado a los clientes seleccionados. |
| **Excepciones** | **Paso 5:** Si los datos son inválidos, se muestran errores de validación debajo de los campos. El usuario puede presionar "Cancelar" para cerrar el formulario sin guardar. |
| **Rendimiento** | Paso 8: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Media |
| **Comentarios** | Ninguno. |

---

#### UC-27B — Editar cupones

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al administrador modificar los datos de un cupón existente: tipo de descuento, valor, descripción, fechas de validez, límite de usos y clientes asignados. |
| **Precondición** | El usuario está autenticado como superadmin u owner. El cupón existe. |
| **Secuencia normal** | **Paso 1:** Desde la tabla de cupones (UC-27E), el administrador presiona el enlace "Editar" en la columna "Acciones" del cupón correspondiente. **Paso 2:** El sistema muestra el formulario "Editar Cupón" con los campos precargados: "Tipo de descuento" (opciones: "Porcentaje (%)" o "Monto Fijo ($)"), "Valor del descuento", "Descripción del cupón", "Válido desde" (selector de fecha), "Válido hasta (opcional)" (selector de fecha), "Máximo de usos (opcional)". **Paso 3:** Debajo aparece la sección "Clientes que reciben el cupón" con los checkboxes de clientes, mostrando pre-seleccionados los ya asignados. **Paso 4:** El administrador modifica los campos deseados y presiona "Guardar Cambios". **Paso 5:** El sistema valida los datos ingresados. **Paso 6:** El sistema actualiza el cupón y redirige al listado con un mensaje de éxito. |
| **Postcondición** | Cupón actualizado con los nuevos datos. |
| **Excepciones** | **Paso 5:** Si los datos son inválidos, se muestran errores de validación debajo de los campos correspondientes. El administrador puede presionar "Cancelar" para volver al listado sin guardar cambios. |
| **Rendimiento** | Paso 6: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Media |
| **Comentarios** | Ninguno. |

---

#### UC-27C — Deshabilitar cupones

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al administrador deshabilitar o eliminar un cupón, impidiendo que sea utilizado en futuras reservas. |
| **Precondición** | El usuario está autenticado como superadmin u owner. El cupón existe. |
| **Secuencia normal** | **Paso 1 (Desactivar):** Desde la tabla de cupones en "Cupones y Descuentos", el administrador presiona el enlace "Desactivar" en la columna "Acciones" del cupón correspondiente. El sistema cambia el estado del cupón a inactivo. La etiqueta en la columna "Estado" cambia a "Inactivo" (rojo) y el enlace de acción cambia a "Activar". **Paso 2 (Activar):** Para reactivar un cupón desactivado, el administrador presiona el enlace "Activar". El estado vuelve a "Activo" (verde). **Paso 3 (Eliminar):** El administrador presiona el enlace "Eliminar" en la columna "Acciones". El sistema muestra el mensaje "¿Estás seguro de que querés eliminar este cupón?". El administrador confirma y el cupón se elimina del listado. |
| **Postcondición** | Cupón desactivado o reactivado. Un cupón inactivo no puede ser aplicado en nuevas reservas. Las reservas que ya lo usaron mantienen su descuento. |
| **Excepciones** | Ninguna. |
| **Rendimiento** | Paso 1: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Media |
| **Comentarios** | Ninguno. |

---

#### UC-27D — Eliminar cupones

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al administrador eliminar permanentemente un cupón existente del sistema. |
| **Precondición** | El usuario está autenticado como superadmin u owner. El cupón existe. |
| **Secuencia normal** | **Paso 1:** Desde la tabla de cupones (UC-27E), el administrador presiona el enlace "Eliminar" en la columna "Acciones" del cupón correspondiente. **Paso 2:** El sistema muestra el mensaje de confirmación "¿Estás seguro de que querés eliminar este cupón?" junto con el código y descripción del cupón. **Paso 3:** El administrador presiona "Confirmar". **Paso 4:** El sistema elimina el cupón del listado. **Paso 5:** El sistema redirige al listado con un mensaje de éxito. |
| **Postcondición** | Cupón eliminado del sistema. Las reservas que ya lo usaron mantienen su descuento. |
| **Excepciones** | **Paso 3:** Si el administrador presiona "Cancelar", se cierra el modal de confirmación sin realizar ninguna acción. |
| **Rendimiento** | Paso 4: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Media |
| **Comentarios** | La eliminación es permanente (soft delete en base de datos). No se puede recuperar un cupón eliminado desde la interfaz. |

---

#### UC-27E — Ver cupones

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-06 Fidelizar a los clientes |
| **Requisitos asociados** | IRQ-02 Información de reservas |
| **Descripción** | Permite al administrador visualizar los cupones existentes en una tabla con toda su información. |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Cupones y Descuentos", el sistema muestra la tabla de cupones con las columnas: "Código", "Descripción", "Descuento", "Clientes", "Usos", "Validez", "Estado" y "Acciones". **Paso 2:** La columna "Estado" muestra la etiqueta "Activo" (verde) o "Inactivo" (rojo). **Paso 3:** La columna "Acciones" muestra los enlaces: "Editar" (UC-27B), "Desactivar"/"Activar" (UC-27C) y "Eliminar" (UC-27D). **Paso 4:** El administrador puede buscar cupones usando el campo de búsqueda (placeholder: "Buscar por código o descripción..."). |
| **Postcondición** | El administrador visualiza los cupones existentes con toda su información. |
| **Excepciones** | **Paso 1:** Si no hay cupones creados, se muestra el mensaje "No hay cupones creados aún." con el texto secundario "Creá tu primer cupón para ofrecer descuentos a tus clientes." |
| **Rendimiento** | Paso 1: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-28 — Gestionar Staff

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios, IRQ-06 Información de complejos |
| **Descripción** | Caso de uso agrupador que permite al owner crear cuentas de staff, asignarlas a sus complejos y revocar acceso. El staff solo ve el complejo al que está asignado. |
| **Precondición** | El usuario está autenticado como owner o superadmin. Tiene al menos un complejo registrado. |
| **Secuencia normal** | **Paso 1:** El owner selecciona "Staff" en el menú lateral (sección "Mi Complejo"). **Paso 2:** El sistema muestra el listado del staff asignado a los complejos del owner. Incluye búsqueda por nombre/email. **Paso 3:** Si el usuario quiere: a) Crear cuenta staff → ver UC-28A. b) Asignar staff a complejo → ver UC-28B. c) Revocar acceso → ver UC-28C. |
| **Postcondición** | El owner puede gestionar al staff asociado. |
| **Excepciones** | Ninguna. |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-28A — Crear cuenta Staff

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios, IRQ-06 Información de complejos |
| **Descripción** | El owner puede crear cuentas de usuario con rol staff y asignarlas a uno o varios de sus complejos. |
| **Precondición** | El usuario está autenticado como owner o superadmin. Tiene al menos un complejo registrado. |
| **Secuencia normal** | **Paso 1:** El owner selecciona "Staff" en el menú lateral (sección "Mi Complejo"). **Paso 2:** El sistema muestra la pantalla "Gestionar Staff" (ver UC-28B para el listado). El owner presiona el botón "Crear Staff". **Paso 3:** El sistema muestra la pantalla "Crear Staff" con un enlace "Volver" al listado. Se presentan los campos: "Nombre", "Email" y "Password". **Paso 4:** Debajo aparece la sección "Asignar a complejos" con una lista de checkboxes, uno por cada complejo del owner. Cada checkbox muestra el nombre del complejo y opcionalmente su dirección. **Paso 5:** El owner completa los campos del formulario y selecciona uno o varios complejos. **Paso 6:** El owner presiona el botón "Crear Staff". **Paso 7:** El sistema valida los datos y verifica que los complejos seleccionados pertenecen al owner autenticado. **Paso 8:** El sistema crea la cuenta con rol staff y la asigna a los complejos seleccionados. **Paso 9:** El sistema redirige al listado de staff con un mensaje de éxito. |
| **Postcondición** | Cuenta de staff creada y asignada a los complejos seleccionados. |
| **Excepciones** | **Paso 7:** Si el email ya existe, el sistema muestra un error de validación debajo del campo "Email". |
| **Rendimiento** | Paso 8: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-28B — Asignar Staff a complejo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios, IRQ-06 Información de complejos |
| **Descripción** | Permite al owner visualizar el staff asignado a sus complejos. La asignación inicial se realiza durante la creación del staff (UC-28A). |
| **Precondición** | El usuario está autenticado como owner o superadmin. |
| **Secuencia normal** | **Paso 1:** El owner selecciona "Staff" en el menú lateral (sección "Mi Complejo"). **Paso 2:** El sistema muestra la pantalla "Gestionar Staff" con un campo de búsqueda (placeholder: "Buscar por nombre o email...") y el botón "Crear Staff". **Paso 3:** El sistema muestra una tabla con las columnas: "Nombre", "Email", "Complejos Asignados" y "Acciones". **Paso 4:** La columna "Complejos Asignados" muestra una etiqueta por cada complejo al que está asignado el staff, con el formato "[Nombre del Complejo] ×" (donde × es un botón para remover). Si el staff no tiene complejos asignados, muestra "Sin complejos asignados". **Paso 5:** La asignación de staff a complejos se realiza en la creación (UC-28A) mediante la sección "Asignar a complejos" del formulario de creación. |
| **Postcondición** | El owner visualiza el staff con sus complejos asignados. |
| **Excepciones** | **Paso 3:** Si no hay staff registrado, se muestra el mensaje "No hay staff registrado." |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-28C — Revocar acceso Staff

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos |
| **Requisitos asociados** | IRQ-03 Información de usuarios, IRQ-06 Información de complejos |
| **Descripción** | Permite al owner revocar el acceso de un staff a un complejo específico, removiendo la asignación. |
| **Precondición** | El usuario está autenticado como owner o superadmin. El staff está asignado a al menos un complejo del owner. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Gestionar Staff" (UC-28B), el owner ubica al staff en la tabla. **Paso 2:** En la columna "Complejos Asignados", el owner hace clic en el botón "×" junto al nombre del complejo del cual desea remover al staff. **Paso 3:** El sistema muestra un cuadro de confirmación con el mensaje "¿Remover a [nombre del staff] del complejo [nombre del complejo]?". **Paso 4:** El owner confirma la acción. **Paso 5:** El sistema elimina la asignación del staff al complejo. La etiqueta del complejo desaparece de la columna "Complejos Asignados". Se muestra un mensaje de éxito. |
| **Postcondición** | Staff removido del complejo. Ya no puede ver las reservas de ese complejo en "Reservas del Dia". |
| **Excepciones** | Ninguna relevante. |
| **Rendimiento** | Paso 5: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-29 — Gestionar complejos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos, OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-06 Información de complejos |
| **Descripción** | Caso de uso agrupador que permite al owner gestionar sus complejos deportivos. Incluye: crear (UC-29A), editar (UC-29B), eliminar (UC-29C) y ver complejos (UC-29D). Cada complejo agrupa canchas y staff bajo un mismo owner. |
| **Precondición** | El usuario está autenticado como owner o superadmin. |
| **Secuencia normal** | Ver sub-casos UC-29A a UC-29D. |
| **Postcondición** | Según la acción seleccionada (ver sub-casos). |
| **Excepciones** | Ver sub-casos. |
| **Rendimiento** | Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-29A — Crear complejo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos, OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-06 Información de complejos |
| **Descripción** | Permite al owner crear un nuevo complejo deportivo con nombre y dirección. |
| **Precondición** | El usuario está autenticado como owner o superadmin. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis Complejos" (UC-29D), el owner presiona el botón "Crear Complejo". **Paso 2:** El sistema muestra la pantalla "Crear Complejo" con un enlace "Volver" al listado y los campos: "Nombre" y "Direccion". **Paso 3:** El owner completa los campos y presiona "Crear Complejo". **Paso 4:** El sistema valida los datos. **Paso 5:** El sistema crea el complejo con estado "Activo" y redirige al listado con un mensaje de éxito. |
| **Postcondición** | Complejo creado y activo. Aparece en el listado de complejos del owner. |
| **Excepciones** | **Paso 4:** Si los datos son inválidos, se muestran errores de validación debajo de los campos correspondientes. |
| **Rendimiento** | Paso 5: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-29B — Editar complejo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos, OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-06 Información de complejos |
| **Descripción** | Permite al owner modificar los datos de un complejo existente (nombre, dirección) y cambiar su estado activo/inactivo. |
| **Precondición** | El usuario está autenticado como owner o superadmin. El complejo existe y pertenece al owner. |
| **Secuencia normal** | **Paso 1:** Desde el listado "Mis Complejos" (UC-29D), el owner presiona "Editar" en la columna "Acciones". **Paso 2:** El sistema muestra la pantalla "Editar Complejo" con los campos precargados: "Nombre" y "Direccion". **Paso 3:** El owner modifica los campos deseados y presiona "Guardar Cambios". **Paso 4:** El sistema valida los datos. **Paso 5:** El sistema actualiza el complejo y redirige al listado con un mensaje de éxito. **Paso 6 (Activar/Desactivar):** El owner presiona "Desactivar" o "Activar" en la columna "Acciones" del listado. El sistema cambia el estado del complejo y actualiza la tabla con un mensaje de éxito. |
| **Postcondición** | Complejo actualizado con los nuevos datos o con el estado cambiado. |
| **Excepciones** | **Paso 4:** Si los datos son inválidos, se muestran errores de validación debajo de los campos. |
| **Rendimiento** | Paso 5/6: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-29C — Eliminar complejo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos, OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-06 Información de complejos |
| **Descripción** | Permite al owner eliminar permanentemente un complejo de su cuenta. |
| **Precondición** | El usuario está autenticado como owner o superadmin. El complejo existe y pertenece al owner. |
| **Secuencia normal** | **Paso 1:** Desde el listado "Mis Complejos" (UC-29D), el owner presiona "Eliminar" en la columna "Acciones". **Paso 2:** El sistema muestra un mensaje de confirmación "¿Estás seguro de que querés eliminar el complejo [nombre]? Esta acción no se puede deshacer." **Paso 3:** El owner presiona "Confirmar". **Paso 4:** El sistema elimina el complejo (soft delete) y redirige al listado con un mensaje de éxito. |
| **Postcondición** | Complejo eliminado. Las canchas y staff asociados quedan sin complejo activo. |
| **Excepciones** | **Paso 3:** Si el owner presiona "Cancelar", se cierra el modal sin realizar ninguna acción. |
| **Rendimiento** | Paso 4: Menos de 1 segundo. |
| **Frecuencia** | Muy pocas veces. |
| **Estabilidad** | Alta |
| **Comentarios** | La eliminación es permanente (soft delete en base de datos). |

---

#### UC-29D — Ver complejos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-07 Gestionar roles y permisos, OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-06 Información de complejos |
| **Descripción** | Permite al owner visualizar el listado de sus complejos con toda su información. |
| **Precondición** | El usuario está autenticado como owner o superadmin. |
| **Secuencia normal** | **Paso 1:** El owner selecciona "Complejos" en el menú lateral (sección "Mi Complejo"). **Paso 2:** El sistema muestra la pantalla "Mis Complejos" con el botón "Crear Complejo". **Paso 3:** El sistema muestra una tabla con las columnas: "Nombre", "Direccion", "Canchas" (cantidad), "Staff" (cantidad), "Estado" y "Acciones". La columna "Estado" muestra: "Activo" (verde) o "Inactivo" (rojo). **Paso 4:** La columna "Acciones" muestra los enlaces: "Editar" (UC-29B), "Desactivar"/"Activar" (UC-29B) y "Eliminar" (UC-29C). |
| **Postcondición** | El owner visualiza sus complejos con toda la información relevante. |
| **Excepciones** | **Paso 3:** Si no hay complejos registrados, se muestra el mensaje "No hay complejos registrados." |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-30 — Gestionar cancha

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Caso de uso agrupador que permite al administrador/owner gestionar las canchas de sus complejos. Incluye: ver (UC-30A), editar (UC-30B), crear (UC-30C), deshabilitar (UC-30D), eliminar (UC-30E) y definir horarios (UC-30F). |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | Ver sub-casos UC-30A a UC-30F. |
| **Postcondición** | Según la acción seleccionada (ver sub-casos). |
| **Excepciones** | Ver sub-casos. |
| **Rendimiento** | Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-30A — Ver canchas

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Permite al administrador/owner visualizar el listado de sus canchas. |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | **Paso 1:** El administrador selecciona "Mis Canchas" en el menú lateral (sección "Mi Complejo"). **Paso 2:** El sistema muestra la pantalla "Mis canchas" con el botón "Crear cancha" y un listado de tarjetas. **Paso 3:** Cada tarjeta de cancha muestra: nombre de la cancha, tipo (etiqueta: "Futbol" o "Padel"), dirección (o "Sin dirección" si no tiene), "Jugadores: [número]", y los botones "Editar" y el enlace "Ver horarios". |
| **Postcondición** | El administrador visualiza sus canchas. |
| **Excepciones** | **Paso 2:** Si no tiene canchas, no se muestran tarjetas. |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-30B — Editar cancha

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Permite al administrador/owner modificar la información de una cancha existente. |
| **Precondición** | El usuario está autenticado como superadmin u owner. La cancha existe. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis canchas" (UC-30A), el administrador presiona el botón "Editar" en la tarjeta de la cancha correspondiente. **Paso 2:** El sistema abre el modal "Editar Cancha" con dos secciones. **Sección "Información Básica":** campos "Nombre", "Precio", "Tipo" (opciones: "Fútbol", "Pádel") y "Cantidad de jugadores". **Sección "Dirección":** campos "Calle", "Número", "Ciudad", "Provincia", "Código Postal" y "País". Todos los campos vienen precargados con los datos actuales de la cancha. **Paso 3:** El administrador modifica los campos deseados. **Paso 4:** El administrador presiona el botón "Actualizar cancha". **Paso 5:** El sistema muestra un cuadro de confirmación con el mensaje "¿Está seguro de actualizar la cancha?". **Paso 6:** El administrador presiona "Sí, confirmar". **Paso 7:** El sistema valida y actualiza la cancha. Se muestra un mensaje de éxito. El administrador puede presionar "Cancelar" en cualquier momento para cerrar el modal sin cambios. |
| **Postcondición** | Cancha actualizada con los nuevos datos. |
| **Excepciones** | **Paso 7:** Si hay errores de validación, el sistema muestra los mensajes debajo de los campos correspondientes. |
| **Rendimiento** | Paso 7: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-30C — Crear cancha

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Permite al administrador/owner registrar una nueva cancha. |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis canchas" (UC-30A), el administrador presiona el botón "Crear cancha". **Paso 2:** El sistema abre el modal "Nueva Cancha" con dos secciones. **Sección "Información Básica":** campos "Nombre", "Precio", "Tipo" (opciones: "Fútbol", "Pádel") y "Cantidad de jugadores". **Sección "Dirección":** campos "Calle", "Número", "Ciudad", "Provincia", "Código Postal" y "País". **Paso 3:** El administrador completa todos los campos del formulario. **Paso 4:** El administrador presiona el botón "Guardar cancha". **Paso 5:** El sistema muestra un cuadro de confirmación con el mensaje "¿Está seguro de guardar la cancha?". **Paso 6:** El administrador presiona "Sí, confirmar". **Paso 7:** El sistema valida la información y registra la nueva cancha con su dirección. Se muestra un mensaje de éxito y se actualiza el listado de tarjetas. El administrador puede presionar "Cancelar" en cualquier momento para cerrar el modal sin guardar. |
| **Postcondición** | Cancha registrada. |
| **Excepciones** | **Paso 7:** Si hay errores al completar el formulario (campos requeridos vacíos, formato inválido), el sistema muestra los mensajes de validación debajo de los campos correspondientes y no cierra el modal. |
| **Rendimiento** | Paso 7: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-30D — Deshabilitar cancha

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Permite al administrador/owner deshabilitar temporalmente una cancha sin eliminarla. La cancha no aparece como disponible para reservas. |
| **Precondición** | El usuario está autenticado como superadmin u owner. La cancha existe y está activa. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis canchas" (UC-30A), el administrador selecciona la opción de deshabilitar la cancha correspondiente. **Paso 2:** El sistema cambia el estado de la cancha a inactiva. **Paso 3:** La cancha deja de aparecer en la grilla de disponibilidad (UC-02) y en la sección pública de canchas (UC-15). |
| **Postcondición** | Cancha deshabilitada. No disponible para nuevas reservas. |
| **Excepciones** | Ninguna relevante. |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Las reservas existentes no se cancelan automáticamente. |

---

#### UC-30E — Eliminar cancha

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Permite al administrador/owner eliminar una cancha del sistema. Se utiliza borrado lógico para mantener el historial. |
| **Precondición** | El usuario está autenticado como superadmin u owner. La cancha existe. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis canchas" (UC-30A), el administrador selecciona la opción de eliminar la cancha correspondiente. **Paso 2:** El sistema muestra un cuadro de diálogo de confirmación. **Paso 3:** El administrador confirma presionando "Sí, confirmar". **Paso 4:** El sistema realiza un borrado lógico de la cancha. La tarjeta desaparece del listado. |
| **Postcondición** | Cancha eliminada (borrado lógico). No visible en listados ni disponible para reservas. El historial de reservas asociadas se mantiene. |
| **Excepciones** | **Paso 2:** Si la cancha tiene reservas futuras activas, el sistema advierte al administrador antes de confirmar. |
| **Rendimiento** | Paso 4: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al año. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-30F — Definir horarios

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas |
| **Requisitos asociados** | IRQ-01 Información de canchas |
| **Descripción** | Permite al administrador/owner registrar los horarios de atención de una cancha, definiendo turnos de mañana y tarde para cada día de la semana. |
| **Precondición** | El usuario está autenticado como superadmin u owner. Tiene al menos una cancha registrada. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis canchas" (UC-30A), el administrador presiona el enlace "Ver horarios" en la tarjeta de la cancha correspondiente, o presiona el botón "Definir horarios". **Paso 2:** El sistema abre el modal "Configurar Horarios de Atención" con el subtítulo "[Nombre de la cancha] — Define los turnos de mañana y tarde para cada día." **Paso 3:** El modal muestra una tabla con las columnas: "Día", "Estado", "Turno Mañana" y "Turno Tarde (Opcional)". Cada fila corresponde a un día de la semana. **Paso 4:** Para cada día, el administrador puede: activar o desactivar el día (la etiqueta "Cerrado" aparece cuando está desactivado), y definir los horarios de cada turno con los campos "Apertura" y "Cierre" (selectores de hora). **Paso 5:** El administrador completa los horarios deseados y presiona el botón "Guardar configuración" (muestra "Guardando..." mientras se procesa). **Paso 6:** El sistema valida la información (no superposición de horarios entre turnos) y registra los horarios. **Paso 7:** El sistema cierra el modal y muestra un mensaje de éxito. El administrador puede presionar "Cancelar" o "Volver a Canchas" para cerrar sin guardar. |
| **Postcondición** | Horarios de cancha registrados. La cancha aparece disponible en esos horarios para reservas (UC-02). |
| **Excepciones** | **Paso 6:** Si los horarios se superponen o tienen formato inválido, el sistema muestra los errores de validación. |
| **Rendimiento** | Paso 6: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-31 — Ver auditoría

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-03 Facilitar la administración de canchas, OBJ-05 Proveer métricas y reportes |
| **Requisitos asociados** | IRQ-07 Información de auditoría |
| **Descripción** | Permite al superadmin y al owner consultar el registro de auditoría del sistema. El superadmin ve todas las acciones de todos los usuarios. El owner ve únicamente las acciones relacionadas con sus complejos y canchas (CRUDs, pagos, cancelaciones, reembolsos, reservas). La vista presenta una tabla con filtros y permite exportar los resultados como PDF. |
| **Precondición** | El usuario está autenticado como superadmin u owner. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Auditoría" en el menú lateral (sección "Administracion" para superadmin, o "Mi Complejo" para owner). **Paso 2:** El sistema muestra la pantalla "Auditoría" con los filtros: "Usuario" (selector desplegable), "Acción" (opciones: Todos, Creación, Edición, Eliminación, Login, Logout, Pago, Cancelación, Reembolso), "Modelo" (opciones: Todos, Reserva, Cancha, Complejo, Usuario, Cupón, Promoción, Staff), "Fecha desde" (selector de fecha), "Fecha hasta" (selector de fecha). Incluye los botones "Filtrar" y "Exportar PDF". **Paso 3:** El sistema muestra una tabla con las columnas: "Fecha y hora", "Usuario", "Acción", "Modelo afectado", "Detalle" e "IP". La columna "Acción" muestra etiquetas con colores: "Creación" (verde), "Edición" (azul), "Eliminación" (rojo), "Login" (gris), "Pago" (amarillo), "Cancelación" (naranja), "Reembolso" (violeta). La columna "Detalle" muestra un resumen breve del cambio (ej: "Cancha 'Futbol 5 A' editada: precio $5000 → $6000"). **Paso 4:** La tabla se ordena por fecha descendente (acciones más recientes primero) y se pagina con 25 registros por página. **Paso 5 (Filtrar):** El usuario selecciona filtros y presiona "Filtrar". El sistema actualiza la tabla mostrando solo los registros que coinciden. **Paso 6 (Exportar):** El usuario presiona "Exportar PDF". El sistema genera un archivo PDF con los registros actualmente visibles (respetando los filtros aplicados) y lo descarga automáticamente. El PDF incluye: título "Reporte de Auditoría — AposPlay", fecha de generación, filtros aplicados, y la tabla de resultados. |
| **Postcondición** | El usuario visualiza el historial de acciones según su rol. |
| **Excepciones** | **Paso 3:** Si no hay registros que coincidan con los filtros, se muestra el mensaje "No se encontraron registros de auditoría." con el texto secundario "Ajustá los filtros o seleccioná un rango de fechas diferente." **Paso 6:** Si hay más de 1000 registros, el sistema muestra el aviso "El PDF incluirá los primeros 1000 registros. Ajustá los filtros para un reporte más específico." |
| **Rendimiento** | Paso 3: Menos de 2 segundos. Paso 6: Menos de 5 segundos. |
| **Frecuencia** | Varias veces a la semana. |
| **Estabilidad** | Alta |
| **Comentarios** | Visibilidad por rol: **Superadmin** — ve todas las acciones de todos los usuarios del sistema sin restricción. **Owner** — ve únicamente acciones sobre modelos vinculados a sus complejos: reservas en sus canchas, CRUDs de sus canchas y complejos, pagos y reembolsos de reservas en sus canchas, acciones de su staff. El filtro "Usuario" para owner solo muestra usuarios que interactuaron con sus complejos. |

---

#### UC-32 — Gestionar torneos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Caso de uso agrupador que permite al owner crear y administrar torneos deportivos. Incluye: crear (UC-32A), editar (UC-32B), gestionar inscripciones de equipos (UC-32C), generar fixture (UC-32D) y registrar resultados (UC-32E). |
| **Precondición** | El usuario está autenticado como owner o superadmin. |
| **Secuencia normal** | **Paso 1:** El owner selecciona "Mis Torneos" en el menú lateral (sección "Mi Complejo"). **Paso 2:** El sistema muestra el listado de torneos del owner con botones para acceder a cada sub-caso. |
| **Postcondición** | Según la acción seleccionada (ver sub-casos). |
| **Excepciones** | Ver sub-casos. |
| **Rendimiento** | Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-32A — Crear torneo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite al owner registrar un nuevo torneo con todos sus parámetros: deporte, formato, fechas, precio de inscripción y cupo máximo. |
| **Precondición** | El usuario está autenticado como owner o superadmin. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis Torneos" (UC-32), el owner presiona el botón "Crear Torneo". **Paso 2:** El sistema muestra el formulario "Crear Torneo" con los campos: "Nombre" (placeholder: "Ej: Torneo Verano 2026"), "Deporte" (selector: "Fútbol 5", "Pádel", etc.), "Formato" (opciones: "Liga", "Eliminación Directa"), "Precio de inscripción ($)", "Cupo máximo de equipos", "Cancha" (selector de las canchas del owner), "Fecha de inicio" (selector de fecha) y "Fecha de fin" (selector de fecha). **Paso 3:** El owner completa los campos y presiona "Crear Torneo". **Paso 4:** El sistema valida que la fecha de inicio sea futura y que la fecha de fin sea posterior a la de inicio. **Paso 5:** El sistema crea el torneo en estado "Borrador" (draft) y redirige al listado con un mensaje de éxito. |
| **Postcondición** | Torneo creado en estado "Borrador". No visible aún para los usuarios finales. |
| **Excepciones** | **Paso 4:** Si los datos son inválidos, se muestran errores de validación debajo de los campos correspondientes. |
| **Rendimiento** | Paso 5: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Alta |
| **Comentarios** | El torneo permanece en estado "Borrador" hasta que el owner abra la inscripción (UC-32C). |

---

#### UC-32B — Editar torneo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite al owner modificar los datos de un torneo existente mientras está en estado "Borrador" o "Inscripción abierta". |
| **Precondición** | El usuario está autenticado como owner o superadmin. El torneo existe y está en estado "Borrador" o "Inscripción abierta". |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Mis Torneos" (UC-32), el owner presiona "Editar" en la fila del torneo correspondiente. **Paso 2:** El sistema muestra el formulario "Editar Torneo" con los campos precargados (mismos campos que UC-32A). **Paso 3:** El owner modifica los campos deseados y presiona "Guardar Cambios". **Paso 4:** El sistema valida los datos. **Paso 5:** El sistema actualiza el torneo y redirige al listado con un mensaje de éxito. |
| **Postcondición** | Torneo actualizado con los nuevos datos. |
| **Excepciones** | **Paso 1:** Si el torneo está "En progreso" o "Finalizado", el enlace "Editar" no está disponible. **Paso 4:** Si los datos son inválidos, se muestran errores de validación debajo de los campos. |
| **Rendimiento** | Paso 5: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes. |
| **Estabilidad** | Media |
| **Comentarios** | No se pueden modificar el formato ni la cancha una vez que hay equipos inscriptos. |

---

#### UC-32C — Gestionar inscripciones de equipos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite al owner abrir y cerrar el período de inscripción, ver los equipos registrados con su estado de pago, y avanzar el torneo al estado "En progreso" cuando corresponda. |
| **Precondición** | El usuario está autenticado como owner o superadmin. El torneo existe. |
| **Secuencia normal** | **Paso 1:** Desde "Mis Torneos", el owner presiona "Equipos" en la fila del torneo. **Paso 2:** El sistema muestra la pantalla "Gestión de Equipos — [Nombre del torneo]" con el estado actual del torneo y los botones de acción. **Paso 3 (Abrir inscripción):** Si el torneo está en "Borrador", el owner presiona "Abrir Inscripción". El estado cambia a "Inscripción abierta". El torneo es visible para los usuarios (UC-33). **Paso 4 (Cerrar inscripción):** El owner presiona "Cerrar Inscripción". El estado cambia a "Inscripción cerrada". Los usuarios ya no pueden inscribir nuevos equipos. **Paso 5:** El sistema muestra una tabla con las columnas: "Equipo", "Capitán", "Integrantes", "Estado de Pago" y "Acciones". La columna "Estado de Pago" muestra: "Pendiente" (amarillo), "Pagado" (verde) o "Reembolsado" (gris). **Paso 6 (Iniciar torneo):** Cuando la inscripción está cerrada y hay al menos 2 equipos con pago confirmado, el owner puede presionar "Iniciar Torneo". El estado cambia a "En progreso". **Paso 7 (Finalizar torneo):** Una vez jugados todos los partidos del fixture, el owner presiona "Finalizar Torneo". El estado cambia a "Finalizado". |
| **Postcondición** | Estado del torneo actualizado. Los usuarios ven el torneo según su estado. |
| **Excepciones** | **Paso 6:** Si hay menos de 2 equipos con pago confirmado, el botón "Iniciar Torneo" está deshabilitado con el mensaje "Se necesitan al menos 2 equipos con pago confirmado para iniciar el torneo." |
| **Rendimiento** | Cada cambio de estado: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces por torneo. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-32D — Generar fixture

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite al owner generar el fixture del torneo (cuadro de partidos) a partir de los equipos con inscripción confirmada, usando el algoritmo correspondiente al formato del torneo. |
| **Precondición** | El usuario está autenticado como owner o superadmin. El torneo está en estado "En progreso". Hay al menos 2 equipos con pago confirmado. |
| **Secuencia normal** | **Paso 1:** Desde "Mis Torneos", el owner presiona "Fixture" en la fila del torneo. **Paso 2:** El sistema muestra la pantalla "Fixture — [Nombre del torneo]". Si no hay partidos generados, muestra el botón "Generar Fixture". **Paso 3:** El owner presiona "Generar Fixture". El sistema muestra el cuadro de confirmación "¿Confirmar generación del fixture? Esta acción organizará los partidos automáticamente." **Paso 4:** El owner confirma. **Paso 5 (Liga):** El sistema genera n-1 rondas, con n/2 partidos por ronda, usando el algoritmo de rotación estándar. Todos los equipos se enfrentan una vez entre sí. **Paso 5 (Eliminación Directa):** El sistema genera un bracket potencia de 2. Si la cantidad de equipos no es potencia de 2, se asignan "byes" a los equipos con mejor posición de inscripción. **Paso 6:** El sistema muestra la tabla de partidos con las columnas: "Ronda", "Equipo Local", "Equipo Visitante", "Fecha/Hora" y "Resultado". Los partidos sin resultado muestran "Pendiente". **Paso 7:** El owner puede registrar la fecha y hora de cada partido haciendo clic en la celda correspondiente. |
| **Postcondición** | Fixture generado y visible para todos los participantes (UC-35). |
| **Excepciones** | **Paso 2:** Si ya existe un fixture, se muestra la advertencia "Ya existe un fixture generado. Regenerar eliminará todos los resultados registrados." con los botones "Regenerar" y "Cancelar". |
| **Rendimiento** | Paso 5: Menos de 2 segundos. |
| **Frecuencia** | Una vez por torneo. |
| **Estabilidad** | Alta |
| **Comentarios** | La regeneración del fixture borra los resultados previos. |

---

#### UC-32E — Registrar resultado de partido

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite al owner cargar el resultado de un partido del fixture e ingresar las estadísticas individuales de los jugadores (goles, asistencias, tarjetas). |
| **Precondición** | El usuario está autenticado como owner o superadmin. El torneo está "En progreso". El fixture ha sido generado. |
| **Secuencia normal** | **Paso 1:** Desde la pantalla "Fixture" (UC-32D), el owner presiona el botón "Cargar Resultado" en la fila del partido correspondiente. **Paso 2:** El sistema muestra el panel de carga con los campos: "Goles [Equipo Local]" (número) y "Goles [Equipo Visitante]" (número). **Paso 3:** Debajo aparece la sección "Estadísticas de Jugadores". El sistema muestra dos columnas, una por equipo. Por cada jugador (integrante registrado del equipo) se muestran los campos: "Goles", "Asistencias", "T. Amarillas" y "T. Rojas". **Paso 4:** El owner completa los scores e ingresa las estadísticas de cada jugador. **Paso 5:** El owner presiona "Guardar Resultado". **Paso 6:** El sistema valida que los goles del score coincidan con la suma de goles de los jugadores de cada equipo. **Paso 7:** El sistema guarda el resultado, actualiza el estado del partido a "Finalizado" y recalcula el standing en tiempo real. Se muestra un mensaje de éxito. |
| **Postcondición** | Resultado registrado. Standing y estadísticas de jugadores actualizados automáticamente. |
| **Excepciones** | **Paso 6:** Si la suma de goles de jugadores no coincide con el score, el sistema muestra la advertencia "Los goles del marcador no coinciden con la suma de goles individuales. ¿Confirmar de todas formas?" con los botones "Confirmar" y "Corregir". |
| **Rendimiento** | Paso 7: Menos de 1 segundo. |
| **Frecuencia** | Varias veces por torneo. |
| **Estabilidad** | Alta |
| **Comentarios** | Las estadísticas de jugadores son opcionales; el score es obligatorio. |

---

#### UC-33 — Ver listado de torneos

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite a cualquier usuario autenticado explorar los torneos disponibles con sus datos principales y estado actual. |
| **Precondición** | El usuario está autenticado. |
| **Secuencia normal** | **Paso 1:** El usuario selecciona "Torneos" en el menú lateral. **Paso 2:** El sistema muestra la pantalla "Torneos" con una grilla de tarjetas. **Paso 3:** Cada tarjeta muestra: nombre del torneo, deporte, formato (etiqueta "Liga" o "Elim. Directa"), estado (etiqueta con color: "Inscripción Abierta" verde, "En Progreso" azul, "Finalizado" gris, "Borrador" amarillo), precio de inscripción, cupo (equipos inscriptos / máximo), y fechas de inicio y fin. **Paso 4:** Los torneos en estado "Inscripción abierta" muestran el botón "Inscribirse" (UC-34). Los torneos en otros estados muestran el botón "Ver detalles" (UC-35). |
| **Postcondición** | El usuario visualiza los torneos disponibles. |
| **Excepciones** | **Paso 2:** Si no hay torneos activos, se muestra el mensaje "No hay torneos disponibles en este momento." |
| **Rendimiento** | Paso 2: Menos de 1 segundo. |
| **Frecuencia** | Varias veces al día. |
| **Estabilidad** | Alta |
| **Comentarios** | Los torneos en estado "Borrador" no se muestran a los usuarios con rol `user`. |

---

#### UC-34 — Inscribirse en torneo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Caso de uso agrupador que permite a un usuario inscribir su equipo en un torneo con inscripción abierta. Comprende: registrar el equipo (UC-34A) y pagar la inscripción (UC-34B). |
| **Precondición** | El usuario está autenticado con rol `user`. El torneo está en estado "Inscripción abierta" y no ha alcanzado el cupo máximo. |
| **Secuencia normal** | Ver sub-casos UC-34A y UC-34B. |
| **Postcondición** | Equipo inscripto. Inscripción pendiente de pago hasta completar UC-34B. |
| **Excepciones** | Si el torneo ya alcanzó el cupo máximo, el botón "Inscribirse" está deshabilitado con el mensaje "Cupo completo." |
| **Rendimiento** | Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes por usuario. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-34A — Registrar equipo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite al usuario registrar el nombre del equipo y agregar los integrantes (con número de camiseta y posición opcionales) en un wizard de 3 pasos. |
| **Precondición** | El usuario está autenticado con rol `user`. El torneo está en estado "Inscripción abierta". |
| **Secuencia normal** | **Paso 1 (Información del equipo):** El sistema muestra el Paso 1 del wizard "Inscribirse en [Nombre del torneo]" con el campo: "Nombre del equipo" (placeholder: "Ej: Los Cóndores"). El usuario completa el campo y presiona "Siguiente →". **Paso 2 (Integrantes):** El sistema muestra el Paso 2 "Integrantes del equipo". Aparece la sección "Buscar usuarios" con un campo de texto (placeholder: "Buscar por nombre o email..."). El sistema busca usuarios registrados en tiempo real. El usuario selecciona uno para agregarlo al equipo. Por cada integrante agregado, se muestran los campos opcionales: "N° de camiseta" (número) y "Posición" (texto libre). El usuario puede eliminar integrantes con el botón "×". Una vez completado, el usuario presiona "Siguiente →". **Paso 3 (Confirmación):** El sistema muestra un resumen con el nombre del equipo, la lista de integrantes, el torneo y el precio de inscripción. El usuario presiona "Confirmar y continuar al pago". **Paso 4:** El sistema registra el equipo con estado de pago "Pendiente" y redirige al paso de pago (UC-34B). |
| **Postcondición** | Equipo registrado en el torneo con estado de pago "Pendiente". |
| **Excepciones** | **Paso 1:** Si el nombre del equipo ya existe en el torneo, el sistema muestra el error "Ya existe un equipo con ese nombre en este torneo." **Paso 2:** El usuario puede avanzar con 0 integrantes adicionales (solo el capitán). La cantidad máxima de integrantes no puede superar el límite configurado del deporte. |
| **Rendimiento** | Paso 4: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces al mes por usuario. |
| **Estabilidad** | Alta |
| **Comentarios** | El usuario que inicia la inscripción queda registrado automáticamente como capitán del equipo. |

---

#### UC-34B — Pagar inscripción de equipo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos, OBJ-04 Gestionar pagos y reembolsos |
| **Requisitos asociados** | IRQ-08 Información de torneos, IRQ-04 Información de pagos y reembolsos |
| **Descripción** | Permite al capitán del equipo pagar la inscripción al torneo mediante MercadoPago. |
| **Precondición** | El equipo está registrado con estado de pago "Pendiente". El usuario autenticado es el capitán del equipo. |
| **Secuencia normal** | **Paso 1:** En el Paso 3 del wizard (UC-34A), el usuario presiona "Confirmar y continuar al pago". **Paso 2:** El sistema genera una preferencia de pago en MercadoPago con `external_reference = "tournament_team_{id}"` y el monto del precio de inscripción del torneo. **Paso 3:** El sistema redirige al usuario a la pantalla de pago de MercadoPago. **Paso 4:** El usuario completa el pago en MercadoPago. **Paso 5:** MercadoPago redirige al usuario a `/torneos/payment/success`. El sistema actualiza el estado de pago del equipo a "Pagado". **Paso 6:** El sistema redirige al listado de torneos (UC-33) con el mensaje "¡Pago de inscripción realizado con éxito!" |
| **Postcondición** | Estado de pago del equipo cambiado a "Pagado". El owner visualiza el equipo como confirmado en UC-32C. |
| **Excepciones** | **Paso 4 (Pago rechazado):** MercadoPago redirige a `/torneos/payment/failure`. El sistema muestra el mensaje "El pago fue rechazado." El equipo permanece en estado "Pendiente". **Paso 4 (Pago pendiente):** MercadoPago redirige a `/torneos/payment/pending`. El sistema muestra el mensaje "El pago está pendiente." El equipo permanece en estado "Pendiente". |
| **Rendimiento** | Paso 2: Menos de 2 segundos. |
| **Frecuencia** | Una vez por equipo por torneo. |
| **Estabilidad** | Alta |
| **Comentarios** | Ninguno. |

---

#### UC-34C — Dar de baja equipo del torneo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite al capitán de un equipo darlo de baja del torneo mientras las inscripciones estén abiertas. El owner también puede dar de baja cualquier equipo desde el panel de gestión. La política de reembolso depende del tiempo restante hasta el inicio del torneo: si faltan más de 36 horas se registra el reembolso; si faltan menos de 36 horas la baja procede pero sin reembolso (la inscripción se pierde). |
| **Precondición** | Para el capitán: el usuario está autenticado, es capitán del equipo y el torneo está en estado "Borrador" o "Inscripción abierta". Para el owner: el usuario está autenticado como owner o superadmin, y el torneo está en estado "Borrador" o "Inscripción abierta". |
| **Secuencia normal (Capitán)** | **Paso 1:** El usuario accede al detalle del torneo (UC-35) y selecciona la pestaña "Equipos". **Paso 2:** El sistema muestra la tarjeta del equipo del capitán con el botón "Dar de baja mi equipo". Si faltan menos de 36hs y el equipo pagó, se muestra el aviso "Faltan menos de 36hs — si te das de baja no se reembolsará la inscripción." **Paso 3:** El capitán presiona el botón. El sistema muestra un mensaje de confirmación que indica si habrá reembolso o no según las horas restantes. **Paso 4:** El capitán confirma. **Paso 5:** El sistema evalúa las horas restantes hasta el inicio del torneo. Si faltan ≥36hs y el equipo había pagado, registra el estado de pago como "Reembolsado". Si faltan <36hs, no se registra reembolso. **Paso 6:** El sistema elimina al equipo y a todos sus integrantes (borrado lógico). La tarjeta desaparece. Se muestra un mensaje de éxito. |
| **Secuencia normal (Owner)** | **Paso 1:** El owner accede a `/owner/torneos/{id}/equipos`. **Paso 2:** El sistema muestra la tabla de equipos. En la columna "Acciones" aparece el botón "Dar de baja". Si faltan <36hs y el equipo pagó, se muestra la etiqueta "sin reembolso" junto al botón. **Paso 3:** El owner presiona "Dar de baja". El sistema muestra un mensaje de confirmación indicando si habrá reembolso o no. **Paso 4:** El owner confirma. **Paso 5-6:** Igual que la secuencia del capitán. |
| **Postcondición** | Equipo eliminado del torneo. El cupo se libera para nuevas inscripciones. Si corresponde, el estado de pago queda como "Reembolsado" (la devolución efectiva del dinero la gestiona el owner manualmente). |
| **Excepciones** | **Paso 2 (Capitán):** Si el torneo ya está "En progreso" o "Finalizado", el botón "Dar de baja mi equipo" no se muestra. **Paso 4:** Si el usuario presiona "Cancelar", no se realiza ninguna acción. |
| **Rendimiento** | Paso 5: Menos de 1 segundo. |
| **Frecuencia** | Pocas veces por torneo. |
| **Estabilidad** | Media |
| **Comentarios** | Regla de reembolso: **≥36hs antes del inicio** → baja con reembolso total. **<36hs antes del inicio** → baja sin reembolso (la inscripción queda retenida). El no-show el día del torneo no genera ninguna acción automática; el owner registra el resultado del partido como "walkover" al cargar el fixture normalmente. |

---

#### UC-35 — Ver detalle y estadísticas de torneo

| Campo                | Detalle |
|----------------------|---------|
| **Objetivos asociados** | OBJ-08 Gestionar torneos deportivos |
| **Requisitos asociados** | IRQ-08 Información de torneos |
| **Descripción** | Permite a cualquier usuario autenticado ver el detalle completo de un torneo en una vista con 4 pestañas: información general, equipos, fixture/standings y estadísticas de jugadores. |
| **Precondición** | El usuario está autenticado. El torneo existe. |
| **Secuencia normal** | **Paso 1:** El usuario presiona "Ver detalles" en la tarjeta del torneo (UC-33) o ingresa directamente a la URL del torneo. **Paso 2:** El sistema muestra la pantalla "Torneo: [Nombre]" con el encabezado que incluye: nombre, deporte, formato, estado (etiqueta), precio, cupo y fechas. **Pestaña "Información":** Muestra los datos del torneo con etiquetas de estado, formato, cancha, owner y fechas. Si el torneo tiene inscripción abierta, muestra el botón "Inscribirse" (UC-34). **Pestaña "Equipos":** Muestra una tabla con los equipos inscriptos: "Equipo", "Capitán", "Integrantes" (cantidad) y "Estado de Pago" (etiqueta). Solo los equipos con pago confirmado (estado "Pagado") se cuentan para el fixture. **Pestaña "Fixture / Standings":** Muestra el fixture con los partidos organizados por rondas (si es Liga) o por fases (si es Eliminación Directa). Para Liga también muestra la tabla de standings con las columnas: "Equipo", "PJ", "PG", "PE", "PP", "GF", "GC", "Pts", ordenada por puntos descendente. Los partidos pendientes muestran "vs" entre los equipos; los finalizados muestran el score. **Pestaña "Estadísticas":** Muestra una tabla de jugadores con las columnas: "Jugador", "Equipo", "Goles", "Asistencias", "T. Amarillas", "T. Rojas" y "Min. Jugados", ordenada por goles descendente. |
| **Postcondición** | El usuario visualiza la información completa y actualizada del torneo. |
| **Excepciones** | **Pestaña "Fixture":** Si el fixture no ha sido generado aún, se muestra el mensaje "El fixture aún no ha sido generado." **Pestaña "Estadísticas":** Si no hay partidos finalizados, se muestra el mensaje "No hay estadísticas disponibles aún." |
| **Rendimiento** | Menos de 1 segundo por pestaña. |
| **Frecuencia** | Varias veces al día durante el torneo. |
| **Estabilidad** | Alta |
| **Comentarios** | La tabla de standings y las estadísticas se recalculan automáticamente al registrar cada resultado (UC-32E). |

---

### Requisitos No funcionales

| ID     | Nombre       | Objetivos asociados                         | Descripción                                                                                                      | Comentarios |
|--------|--------------|---------------------------------------------|------------------------------------------------------------------------------------------------------------------|-------------|
| NFR-01 | Seguridad    | OBJ-06                                      | El sistema deberá incorporar mecanismo de protección CSRF. Autenticación con hashing de contraseñas (bcrypt). Middleware de roles. | Ninguno     |
| NFR-02 | Rendimiento  | OBJ-06                                      | El sistema deberá permitir un rendimiento aceptable para una cantidad demandante de usuarios concurrentes.       | Ninguno     |
| NFR-03 | Usabilidad   | OBJ-06                                      | El sistema tendrá un diseño responsivo (Tailwind CSS v4 + Flux UI).                                              | Ninguno     |
| NFR-04 | Auditoría    | OBJ-03, OBJ-05                              | El sistema deberá registrar todas las acciones relevantes de los usuarios (CRUDs, login/logout, pagos, cancelaciones, reembolsos) en una tabla de auditoría con borrado lógico + timestamps en todos los modelos. El superadmin accede a la auditoría completa; el owner accede solo a las acciones de sus complejos. Se permite exportación a PDF (UC-31, IRQ-07). | Ninguno     |

---

## Matriz de Rastreabilidad Objetivo/Requisitos

|          | OBJ-01 | OBJ-02 | OBJ-03 | OBJ-04 | OBJ-05 | OBJ-06 | OBJ-07 | OBJ-08 |
|----------|--------|--------|--------|--------|--------|--------|--------|--------|
| IRQ-01   |        |        | ✅      |        |        |        |        |        |
| IRQ-02   | ✅      | ✅      |        |        |        |        |        |        |
| IRQ-03   | ✅      |        |        |        |        | ✅      |        |        |
| IRQ-04   |        |        |        | ✅      | ✅      |        |        |        |
| IRQ-05   |        |        |        |        | ✅      |        |        |        |
| IRQ-06   |        |        |        |        |        |        | ✅      |        |
| IRQ-07   |        |        | ✅      |        | ✅      |        |        |        |
| IRQ-08   |        |        |        |        |        |        |        | ✅      |
| UC-01    | ✅      |        |        |        |        |        |        |        |
| UC-01A   | ✅      |        |        |        |        |        |        |        |
| UC-01B   | ✅      |        |        |        |        |        |        |        |
| UC-01C   | ✅      |        |        |        |        |        |        |        |
| UC-02    | ✅      | ✅      |        |        |        |        |        |        |
| UC-03    | ✅      | ✅      |        |        |        |        |        |        |
| UC-04    | ✅      | ✅      |        |        |        |        |        |        |
| UC-05    | ✅      |        |        |        |        |        |        |        |
| UC-06    | ✅      | ✅      |        |        |        |        |        |        |
| UC-07    |        |        |        | ✅      |        |        |        |        |
| UC-08    |        |        |        |        |        | ✅      |        |        |
| UC-09    |        |        |        |        |        | ✅      |        |        |
| UC-10    |        |        |        |        |        | ✅      |        |        |
| UC-11    |        |        |        |        |        | ✅      |        |        |
| UC-12    |        | ✅      |        |        |        |        |        |        |
| UC-13    |        | ✅      |        |        |        |        |        |        |
| UC-14    |        |        |        |        |        | ✅      |        |        |
| UC-15    | ✅      | ✅      |        |        |        |        |        |        |
| UC-16    |        |        |        | ✅      |        |        |        |        |
| UC-17    |        | ✅      |        |        | ✅      |        |        |        |
| UC-18    |        |        |        |        |        |        | ✅      |        |
| UC-19    |        |        |        |        |        |        | ✅      |        |
| UC-20    |        |        |        |        |        |        | ✅      |        |
| UC-21    |        |        |        |        |        |        | ✅      |        |
| UC-22    |        |        |        |        |        |        | ✅      |        |
| UC-23    |        |        |        | ✅      |        |        |        |        |
| UC-24    |        |        |        | ✅      |        |        |        |        |
| UC-25    |        |        |        |        | ✅      |        |        |        |
| UC-26    |        |        |        |        | ✅      |        |        |        |
| UC-27    |        |        |        |        |        | ✅      |        |        |
| UC-27A   |        |        |        |        |        | ✅      |        |        |
| UC-27B   |        |        |        |        |        | ✅      |        |        |
| UC-27C   |        |        |        |        |        | ✅      |        |        |
| UC-27D   |        |        |        |        |        | ✅      |        |        |
| UC-27E   |        |        |        |        |        | ✅      |        |        |
| UC-28    |        |        |        |        |        |        | ✅      |        |
| UC-28A   |        |        |        |        |        |        | ✅      |        |
| UC-28B   |        |        |        |        |        |        | ✅      |        |
| UC-28C   |        |        |        |        |        |        | ✅      |        |
| UC-29    |        |        | ✅      |        |        |        | ✅      |        |
| UC-29A   |        |        | ✅      |        |        |        | ✅      |        |
| UC-29B   |        |        | ✅      |        |        |        | ✅      |        |
| UC-29C   |        |        | ✅      |        |        |        | ✅      |        |
| UC-29D   |        |        | ✅      |        |        |        | ✅      |        |
| UC-30    |        |        | ✅      |        |        |        |        |        |
| UC-30A   |        |        | ✅      |        |        |        |        |        |
| UC-30B   |        |        | ✅      |        |        |        |        |        |
| UC-30C   |        |        | ✅      |        |        |        |        |        |
| UC-30D   |        |        | ✅      |        |        |        |        |        |
| UC-30E   |        |        | ✅      |        |        |        |        |        |
| UC-30F   |        |        | ✅      |        |        |        |        |        |
| UC-31    |        |        | ✅      |        | ✅      |        |        |        |
| UC-32    |        |        |        |        |        |        |        | ✅      |
| UC-32A   |        |        |        |        |        |        |        | ✅      |
| UC-32B   |        |        |        |        |        |        |        | ✅      |
| UC-32C   |        |        |        |        |        |        |        | ✅      |
| UC-32D   |        |        |        |        |        |        |        | ✅      |
| UC-32E   |        |        |        |        | ✅      |        |        | ✅      |
| UC-33    | ✅      |        |        |        |        |        |        | ✅      |
| UC-34    |        |        |        |        |        |        |        | ✅      |
| UC-34A   |        |        |        |        |        |        |        | ✅      |
| UC-34B   |        |        |        | ✅      |        |        |        | ✅      |
| UC-34C   |        |        |        |        |        |        |        | ✅      |
| UC-35    |        |        |        |        | ✅      |        |        | ✅      |
| NFR-01   |        |        |        |        |        | ✅      |        |        |
| NFR-02   |        |        |        |        |        | ✅      |        |        |
| NFR-03   |        |        |        |        |        | ✅      |        |        |
| NFR-04   |        |        | ✅      |        | ✅      |        |        |        |

---

## Glosario de Términos

| Término       | Categoría | Comentarios                                                                          |
|---------------|-----------|--------------------------------------------------------------------------------------|
| Cancha        | Dominio   | Espacio deportivo reservable (fútbol 5 o pádel).                                     |
| Complejo      | Dominio   | Establecimiento deportivo que agrupa una o más canchas bajo un mismo owner.           |
| Reserva       | Dominio   | Registro de un horario reservado por un usuario en una cancha.                        |
| Cupón         | Dominio   | Código de descuento aplicable a una reserva (porcentaje o monto fijo).               |
| Promoción     | Dominio   | Regla de descuento o beneficio (combo, cupón, puntos extra) con vigencia temporal.   |
| Puntos        | Dominio   | Unidades de fidelidad acumuladas por reservas pagadas y canjeables por descuentos.   |
| MercadoPago   | Técnico   | Pasarela de pago utilizada para procesar cobros y reembolsos.                         |
| Superadmin    | Rol       | Administrador global con acceso total al sistema.                                    |
| Owner         | Rol       | Dueño de uno o más complejos deportivos.                                             |
| Staff         | Rol       | Empleado asignado a un complejo que confirma asistencia y gestiona reservas del día. |
| User          | Rol       | Usuario final que reserva y paga canchas.                                            |
| Torneo        | Dominio   | Competición deportiva organizada por un owner entre equipos registrados.             |
| Equipo        | Dominio   | Grupo de jugadores inscriptos en un torneo, liderado por un capitán.                 |
| Fixture       | Dominio   | Cuadro de partidos generado automáticamente según el formato del torneo.             |
| Liga          | Dominio   | Formato de torneo en el que todos los equipos se enfrentan entre sí una vez.         |
| Eliminación Directa | Dominio | Formato de torneo en bracket; el perdedor queda eliminado en cada ronda.    |
| Standing      | Dominio   | Tabla de posiciones con puntos, goles y partidos jugados de cada equipo.             |
| Capitán       | Dominio   | Usuario que registra el equipo y es responsable del pago de la inscripción.          |
