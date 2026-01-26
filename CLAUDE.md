# Missed Revenue Recovery

## Vision

"Tu clinica no vuelve a perder pacientes por no atender el telefono. Cada intento de contacto se transforma automaticamente en una conversacion que puede terminar en una cita."

**No vendemos software. Vendemos ingresos recuperados.**

### Modelo Mental
```
Llamada Perdida → Lead → Conversacion → Cita → Ingreso Recuperado
```

### Metrica Principal
No es "SMS enviados", es **"Cuanto dinero recuperaste este mes que habrias perdido?"**

---

## Arquitectura

### Estructura de Carpetas
```
app/
├── Enums/
│   ├── UserRole.php
│   ├── LeadStage.php
│   ├── LeadOrigin.php
│   ├── MessageChannel.php
│   ├── MessageDirection.php
│   └── OutcomeType.php
├── Models/
├── Http/Controllers/
│   ├── Webhooks/
│   │   └── TwilioWebhookController.php
│   └── Web/                  (FASE POSTERIOR)
│       ├── DashboardController.php
│       ├── ConversationController.php
│       └── ...
├── UseCases/
│   ├── Webhooks/
│   │   ├── ProcessTwilioVoiceWebhook.php
│   │   └── ProcessTwilioSmsWebhook.php
│   ├── Leads/
│   │   ├── CreateLeadFromMissedCall.php
│   │   └── UpdateLeadOutcome.php
│   ├── Messaging/
│   │   ├── SendFollowUpSms.php
│   │   ├── ReceiveIncomingMessage.php
│   │   ├── SendReplyMessage.php
│   │   └── RenderMessageTemplate.php
│   └── Reports/              (FASE POSTERIOR)
├── Jobs/
│   └── SendScheduledFollowUp.php
└── Traits/
    └── BelongsToClinic.php
```

### Filosofia de Codigo
- **Controlador:** Recibe request, delega a UseCase, retorna response
- **UseCase:** Una clase, un metodo `execute()`, resuelve UN problema
- **Model:** Datos y relaciones, nada mas
- **Job:** Cuando algo debe ir a cola o con delay

### Multi-Tenant
TODAS las tablas relevantes tienen `clinic_id`. TODOS los queries filtran por `clinic_id`.

### Webhooks Idempotentes
Deduplicar por `(provider, provider_event_id)`. Permitir replay seguro.

---

## Entidad Central: Lead

Cada oportunidad de negocio es un Lead. Las llamadas perdidas son solo el primer canal de adquisicion.

### Estados del Lead
```
NEW → CONTACTED → RESPONDED → BOOKED
                           → LOST
```

### Origenes de Lead (extensible)
- `missed_call` (MVP)
- `web_form` (futuro)
- `chat_widget` (futuro)
- `whatsapp_inbound` (futuro)

---

## Base de Datos

### Tablas Multi-tenant
- `clinics` - Tenants principales
- `users` - Usuarios del sistema
- `clinic_user` - Pivot con roles (OWNER, MANAGER, STAFF, READ_ONLY)

### Configuracion
- `clinic_settings` - Configuracion por clinica (avg_ticket, horarios, delays)

### Integraciones
- `integrations` - Credenciales de providers (Twilio, etc)
- `clinic_phone_numbers` - Numeros monitoreados
- `webhook_events` - Log de todos los webhooks (idempotencia + debug)

### CRM Core
- `callers` - Personas que contactan (phone normalizado E.164)
- `leads` - Centro del producto
- `conversations` - Chat asociado a un lead
- `missed_calls` - Evento que origina un lead
- `message_templates` - Plantillas con variables
- `messages` - Mensajes IN/OUT por cualquier canal

### Resultados
- `missed_call_outcomes` - Resultado final de cada lead

---

## Comandos Utiles

### Setup Inicial
```bash
# Clonar y entrar
cd dental-revenue-recovery

# Instalar dependencias
composer install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_DATABASE=dental_revenue_recovery
# DB_USERNAME=root
# DB_PASSWORD=

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed
```

### Desarrollo
```bash
# Correr servidor local
php artisan serve

# Correr migraciones fresh + seed
php artisan migrate:fresh --seed

# Correr colas
php artisan queue:work

# Limpiar caches
php artisan optimize:clear
```

### Testing
```bash
# Correr todos los tests
php artisan test

# Correr test especifico
php artisan test --filter=NombreDelTest
```

---

## Reglas de Codigo

1. **Nombres autoexplicativos** - Si necesitas un comentario para explicar que hace algo, renombralo
2. **Metodos pequenos** - Una funcion hace UNA cosa
3. **Ingles en codigo** - Variables, clases, metodos, todo en ingles
4. **Controllers finos** - Solo reciben request y retornan response, la logica va en UseCases
5. **Multi-tenant siempre** - Nunca olvidar `clinic_id` en queries
6. **Webhooks idempotentes** - Siempre verificar si ya procesamos este evento

---

## Flujo Principal (MVP)

```
1. Twilio POST /webhooks/twilio/voice
2. → TwilioWebhookController
3. → Guardar en webhook_events (idempotencia)
4. → ProcessTwilioVoiceWebhook (UseCase)
5. → Si es missed call: CreateLeadFromMissedCall
6. → Crear Lead + Caller + Conversation
7. → Dispatch SendScheduledFollowUp (Job con delay)
8. → SendFollowUpSms (UseCase)
9. → Guardar Message OUT
10. → Twilio envia SMS
11. → Paciente responde
12. → Twilio POST /webhooks/twilio/sms
13. → ReceiveIncomingMessage (UseCase)
14. → Guardar Message IN
15. → Lead pasa a RESPONDED
16. → Staff ve en dashboard y responde
```

---

## Variables de Template

Las plantillas SMS/WhatsApp soportan:

- `{{clinic_name}}` - Nombre de la clinica
- `{{booking_link}}` - Link de reserva
- `{{business_hours}}` - Horarios de atencion
- `{{caller_phone}}` - Telefono del paciente (formateado)

Ejemplo:
```
Hola, somos {{clinic_name}}. Vimos tu llamada y no pudimos atenderte.
Podes responder este mensaje o reservar aca: {{booking_link}}.
Horarios: {{business_hours}}
```

---

## Futuro (IA + CRM)

El sistema esta preparado para:
- IA asistente (sugerencias de respuesta)
- IA que cierre por chat automaticamente
- IA que cierre por voz
- Auditabilidad completa de acciones

Futuras tablas (NO implementar aun):
- `ai_agents`
- `ai_tasks`
- `ai_actions_log`

El dominio centrado en Lead + logs + eventos permite esta evolucion sin reescribir.
