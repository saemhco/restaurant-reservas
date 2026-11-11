#!/usr/bin/env python3
"""Genera el PDF del caso de cliente + resolución técnica."""

from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    HRFlowable,
    ListFlowable,
    ListItem,
    PageBreak,
    Paragraph,
    Preformatted,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)

OUTPUT = Path(__file__).resolve().parent / "restaurant-reservations-case.pdf"


def build_styles():
    base = getSampleStyleSheet()
    styles = {
        "cover_title": ParagraphStyle(
            "cover_title",
            parent=base["Title"],
            fontSize=22,
            leading=28,
            alignment=TA_CENTER,
            spaceAfter=12,
            textColor=colors.HexColor("#1a1a1a"),
        ),
        "cover_sub": ParagraphStyle(
            "cover_sub",
            parent=base["Normal"],
            fontSize=12,
            leading=16,
            alignment=TA_CENTER,
            textColor=colors.HexColor("#444444"),
            spaceAfter=6,
        ),
        "part": ParagraphStyle(
            "part",
            parent=base["Heading1"],
            fontSize=16,
            leading=20,
            spaceBefore=6,
            spaceAfter=12,
            textColor=colors.HexColor("#0f3d2e"),
        ),
        "h2": ParagraphStyle(
            "h2",
            parent=base["Heading2"],
            fontSize=13,
            leading=17,
            spaceBefore=14,
            spaceAfter=8,
            textColor=colors.HexColor("#1f4d3a"),
        ),
        "h3": ParagraphStyle(
            "h3",
            parent=base["Heading3"],
            fontSize=11,
            leading=14,
            spaceBefore=10,
            spaceAfter=6,
            textColor=colors.HexColor("#2a5c47"),
        ),
        "body": ParagraphStyle(
            "body",
            parent=base["Normal"],
            fontSize=10,
            leading=14,
            alignment=TA_JUSTIFY,
            spaceAfter=8,
        ),
        "bullet": ParagraphStyle(
            "bullet",
            parent=base["Normal"],
            fontSize=10,
            leading=13,
            leftIndent=4,
            spaceAfter=3,
        ),
        "note": ParagraphStyle(
            "note",
            parent=base["Normal"],
            fontSize=9,
            leading=12,
            textColor=colors.HexColor("#555555"),
            spaceAfter=8,
        ),
        "code": ParagraphStyle(
            "code",
            fontName="Courier",
            fontSize=8,
            leading=11,
            backColor=colors.HexColor("#f4f4f4"),
            borderPadding=6,
            spaceBefore=4,
            spaceAfter=10,
        ),
        "footer": ParagraphStyle(
            "footer",
            parent=base["Normal"],
            fontSize=8,
            textColor=colors.grey,
            alignment=TA_CENTER,
        ),
        "table_cell": ParagraphStyle(
            "table_cell",
            parent=base["Normal"],
            fontSize=8,
            leading=11,
        ),
        "table_header": ParagraphStyle(
            "table_header",
            parent=base["Normal"],
            fontSize=8,
            leading=11,
            textColor=colors.white,
            fontName="Helvetica-Bold",
        ),
    }
    return styles


def bullets(items, style):
    return ListFlowable(
        [ListItem(Paragraph(i, style), leftIndent=12, bulletColor=colors.HexColor("#1f4d3a")) for i in items],
        bulletType="bullet",
        start="•",
        leftIndent=15,
        spaceBefore=2,
        spaceAfter=8,
    )


def section_rule():
    return HRFlowable(width="100%", thickness=1, color=colors.HexColor("#c8d9d0"), spaceBefore=4, spaceAfter=10)


def make_table(headers, rows, styles):
    data = [[Paragraph(h, styles["table_header"]) for h in headers]]
    for row in rows:
        data.append([Paragraph(str(c), styles["table_cell"]) for c in row])

    table = Table(data, colWidths=[4.2 * cm] * len(headers) if len(headers) <= 4 else None)
    if len(headers) == 2:
        table._argW = [5.5 * cm, 11 * cm]
    elif len(headers) == 3:
        table._argW = [4.5 * cm, 6 * cm, 6 * cm]
    elif len(headers) == 4:
        table._argW = [3.8 * cm, 4.2 * cm, 4.2 * cm, 4.2 * cm]

    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1f4d3a")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
                ("BACKGROUND", (0, 1), (-1, -1), colors.HexColor("#f7faf8")),
                ("GRID", (0, 0), (-1, -1), 0.4, colors.HexColor("#c8d9d0")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 5),
                ("RIGHTPADDING", (0, 0), (-1, -1), 5),
                ("TOPPADDING", (0, 0), (-1, -1), 4),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
                ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.HexColor("#f7faf8"), colors.white]),
            ]
        )
    )
    return table


def build_pdf():
    styles = build_styles()
    doc = SimpleDocTemplate(
        str(OUTPUT),
        pagesize=A4,
        leftMargin=1.8 * cm,
        rightMargin=1.8 * cm,
        topMargin=1.8 * cm,
        bottomMargin=1.8 * cm,
        title="Caso: API de reservas de restaurante",
        author="restaurant-reservas",
    )
    story = []

    # Portada
    story.append(Spacer(1, 3.5 * cm))
    story.append(Paragraph("Caso práctico", styles["cover_sub"]))
    story.append(Paragraph("Sistema de reservas<br/>para restaurante", styles["cover_title"]))
    story.append(Spacer(1, 0.4 * cm))
    story.append(section_rule())
    story.append(
        Paragraph(
            "Documento para resolver: requisitos del cliente (Parte A)<br/>y resolución técnica de referencia (Parte B).",
            styles["cover_sub"],
        )
    )
    story.append(Spacer(1, 1.2 * cm))
    story.append(
        Paragraph(
            "Stack de referencia: Laravel 12 · API REST · Sanctum · SQLite/MySQL<br/>"
            "Código del proyecto en inglés · Comentarios explicativos en español.",
            styles["note"],
        )
    )
    story.append(PageBreak())

    # ========== PARTE A ==========
    story.append(Paragraph("Parte A — Caso del cliente", styles["part"]))
    story.append(section_rule())
    story.append(
        Paragraph(
            "<b>Contexto.</b> Eres el equipo de desarrollo contratado por “La Mesa Verde”, un restaurante "
            "(con posibilidad de abrir más locales) que hoy toma reservas por teléfono y WhatsApp. "
            "Pierden turnos, se sobrebookean mesas y no tienen una visión clara de la carta ni del personal "
            "asignado a cada local. Quieren una <b>aplicación/API</b> que centralice la operación.",
            styles["body"],
        )
    )

    story.append(Paragraph("1. Quiénes usan el sistema", styles["h2"]))
    story.append(
        Paragraph(
            "El dueño describe tres situaciones de uso cotidianas (sin entrar en detalles técnicos):",
            styles["body"],
        )
    )
    story.append(
        bullets(
            [
                "<b>Clientes finales:</b> deben poder crear una cuenta, ver la carta de un local, "
                "consultar mesas libres para una fecha/hora y hacer o cancelar <i>su</i> reserva. "
                "No deberían ver ni modificar reservas de otras personas.",
                "<b>Personal del local:</b> necesitan gestionar el día a día: confirmar o reasignar reservas, "
                "registrar pedidos asociados a una reserva y consultar totales. No deberían poder "
                "borrar o reconfigurar toda la carta o los locales sin autorización.",
                "<b>Administración / gerencia:</b> deben poder dar de alta locales, mesas, platos, "
                "empleados y clientes; ver el panorama completo y corregir datos cuando haga falta.",
            ],
            styles["bullet"],
        )
    )
    story.append(
        Paragraph(
            "En palabras del cliente: <i>“No cualquiera debería poder cambiar la carta o ver los datos de todos. "
            "Cada quien debe ver y hacer solo lo que le corresponde.”</i>",
            styles["body"],
        )
    )

    story.append(Paragraph("2. Qué deben poder gestionar", styles["h2"]))
    story.append(
        bullets(
            [
                "<b>Locales (restaurantes):</b> nombre, dirección, teléfono y horario de apertura/cierre.",
                "<b>Mesas:</b> número, capacidad y ubicación (interior / terraza), pertenecientes a un local.",
                "<b>Carta (platos):</b> nombre, precio, descripción y tipo (entrada, fuerte, postre).",
                "<b>Empleados:</b> nombre y puesto laboral en el local (mesero, chef, gerente de piso).",
                "<b>Clientes:</b> nombre, correo y teléfono.",
                "<b>Reservas:</b> cliente, mesa, fecha, hora, número de personas y estado "
                "(pendiente, confirmada, cancelada).",
                "<b>Pedidos:</b> asociados a una reserva, con platos, cantidades y precio unitario; "
                "poder consultar el total de una reserva.",
            ],
            styles["bullet"],
        )
    )

    story.append(Paragraph("3. Reglas de negocio que el cliente exige", styles["h2"]))
    story.append(
        bullets(
            [
                "No se puede reservar una mesa que ya esté ocupada (pendiente o confirmada) en la misma fecha y hora.",
                "El número de personas no puede superar la capacidad de la mesa.",
                "Debe existir una forma de listar mesas disponibles filtrando por fecha, hora, local y capacidad mínima.",
                "Debe existir un listado de platos más pedidos.",
                "Las respuestas de la API deben ser claras y consistentes (éxito, error, validación, listados paginados).",
                "Debe haber datos de ejemplo para probar el sistema sin cargar todo a mano.",
            ],
            styles["bullet"],
        )
    )

    story.append(Paragraph("4. Entregables esperados", styles["h2"]))
    story.append(
        bullets(
            [
                "API REST documentada en el propio código (rutas, validaciones, recursos).",
                "Modelos con sus relaciones bien definidas.",
                "Validaciones de entrada (formularios/requests) y mensajes de error comprensibles.",
                "Estructura unificada de respuestas JSON.",
                "Datos semilla (seeders) y factories para pruebas.",
                "Control de acceso: solo usuarios autenticados realizan operaciones sensibles; "
                "cada tipo de usuario hace solo lo permitido.",
                "Pruebas automatizadas de los flujos críticos (registro/login, reserva, denegación de acceso).",
            ],
            styles["bullet"],
        )
    )

    story.append(Paragraph("5. Objetivos de aprendizaje (para el estudiante)", styles["h2"]))
    story.append(
        Paragraph(
            "Al resolver este caso practicarás: modelos Eloquent y relaciones; factories y seeders; "
            "Form Requests y validaciones de negocio; API Resources / respuestas unificadas; "
            "autenticación por tokens; autorización (quién puede hacer qué); middleware; y policies.",
            styles["body"],
        )
    )
    story.append(
        Paragraph(
            "<b>Importante:</b> en esta Parte A el cliente <i>no</i> menciona términos como “roles”, "
            "“permisos”, “middleware” o “policies”. Es tu trabajo interpretar el requisito "
            "“cada quien debe ver y hacer solo lo que le corresponde” e implementarlo en la Parte B / en el código.",
            styles["note"],
        )
    )

    story.append(PageBreak())

    # ========== PARTE B ==========
    story.append(Paragraph("Parte B — Resolución técnica", styles["part"]))
    story.append(section_rule())
    story.append(
        Paragraph(
            "Esta sección interpreta el caso y describe la solución implementada en el repositorio "
            "<b>restaurant-reservas</b>. El código (tablas, rutas, campos, mensajes de validación) está en "
            "<b>inglés</b>; los comentarios de apoyo pueden estar en español.",
            styles["body"],
        )
    )

    story.append(Paragraph("1. Interpretación del requisito de acceso", styles["h2"]))
    story.append(
        Paragraph(
            "El pedido del cliente se traduce en: autenticación con <b>Laravel Sanctum</b> (Bearer token); "
            "modelos propios <b>Role</b> y <b>Permission</b> (N:N) con el usuario; middleware "
            "<font face='Courier'>role</font> y <font face='Courier'>permission</font>; y una "
            "<b>ReservationPolicy</b> para que un customer solo gestione sus reservas. "
            "El campo <font face='Courier'>employees.position</font> (waiter/chef/manager) es el "
            "<b>puesto laboral</b>, distinto del rol de autenticación del sistema.",
            styles["body"],
        )
    )

    story.append(Paragraph("Roles y permisos sembrados", styles["h3"]))
    story.append(
        make_table(
            ["Rol", "Permisos principales"],
            [
                ["admin", "Todos (bypass Gate + todos los permisos)"],
                ["manager", "tables, dishes, employees, customers, reservations.manage, orders.manage"],
                ["staff", "reservations.manage, orders.manage, customers.manage"],
                ["customer", "reservations.own"],
            ],
            styles,
        )
    )
    story.append(Spacer(1, 0.35 * cm))

    story.append(Paragraph("Usuarios demo (password: <font face='Courier'>password</font>)", styles["h3"]))
    story.append(
        make_table(
            ["Email", "Rol"],
            [
                ["admin@demo.test", "admin"],
                ["manager@demo.test", "manager"],
                ["staff@demo.test", "staff"],
                ["customer@demo.test", "customer"],
            ],
            styles,
        )
    )

    story.append(Paragraph("2. Modelo de datos (inglés)", styles["h2"]))
    story.append(
        make_table(
            ["Tabla", "Relaciones clave"],
            [
                ["restaurants", "hasMany tables, dishes, employees"],
                ["tables", "belongsTo restaurant; hasMany reservations"],
                ["customers", "belongsTo user; hasMany reservations"],
                ["dishes", "belongsTo restaurant; belongsToMany orders"],
                ["employees", "belongsTo restaurant, user"],
                ["reservations", "belongsTo customer, table; hasOne order"],
                ["orders", "belongsTo reservation; belongsToMany dishes (order_dish)"],
                ["roles / permissions", "N:N con users y entre sí"],
            ],
            styles,
        )
    )

    story.append(Paragraph("3. Estructura de carpetas relevante", styles["h2"]))
    story.append(
        Preformatted(
            "app/Models/                 # Restaurant, Table, Customer, ...\n"
            "app/Traits/HasRoles.php     # assignRole, hasRole, hasPermission\n"
            "app/Http/Controllers/Api/   # Auth + recursos CRUD\n"
            "app/Http/Requests/          # Validaciones + authorize()\n"
            "app/Http/Resources/         # Transformación JSON\n"
            "app/Http/Responses/         # ApiResponse unificado\n"
            "app/Http/Middleware/        # role / permission\n"
            "app/Policies/               # ReservationPolicy\n"
            "database/factories|seeders/ # Datos de práctica\n"
            "routes/api.php              # Rutas públicas + auth:sanctum",
            styles["code"],
        )
    )

    story.append(Paragraph("4. Respuesta API unificada", styles["h2"]))
    story.append(
        Paragraph(
            "Todas las respuestas pasan por <font face='Courier'>App\\Http\\Responses\\ApiResponse</font>:",
            styles["body"],
        )
    )
    story.append(
        Preformatted(
            "{\n"
            '  "success": true,\n'
            '  "message": "Success",\n'
            '  "data": { ... },\n'
            '  "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 40 }\n'
            "}\n\n"
            "# Error de validación\n"
            "{\n"
            '  "success": false,\n'
            '  "message": "Validation failed",\n'
            '  "errors": { "table_id": ["This table is not available..."] }\n'
            "}",
            styles["code"],
        )
    )

    story.append(Paragraph("5. Endpoints principales", styles["h2"]))
    story.append(
        make_table(
            ["Método", "Ruta", "Acceso"],
            [
                ["POST", "/api/auth/register|login", "Público"],
                ["GET/POST", "/api/auth/me|logout", "auth:sanctum"],
                ["GET", "/api/restaurants, /api/dishes", "Público (lectura)"],
                ["GET", "/api/tables/available", "Público"],
                ["CRUD", "/api/restaurants, tables, ...", "auth + permisos"],
                ["CRUD", "/api/reservations", "auth + policy"],
                ["CRUD", "/api/orders", "permission:orders.manage"],
                ["GET", "/api/reservations/{id}/total", "auth + policy"],
                ["GET", "/api/dishes/most-ordered", "Público"],
            ],
            styles,
        )
    )

    story.append(Paragraph("6. Cómo ejecutar", styles["h2"]))
    story.append(
        Preformatted(
            "composer install\n"
            "cp .env.example .env   # si aplica\n"
            "php artisan key:generate\n"
            "touch database/database.sqlite\n"
            "php artisan migrate:fresh --seed\n"
            "php artisan serve\n"
            "php artisan test",
            styles["code"],
        )
    )

    story.append(Paragraph("Ejemplos curl", styles["h3"]))
    story.append(
        Preformatted(
            "# Login\n"
            "curl -s -X POST http://localhost:8000/api/auth/login \\\n"
            "  -H 'Accept: application/json' -H 'Content-Type: application/json' \\\n"
            "  -d '{\"email\":\"customer@demo.test\",\"password\":\"password\"}'\n\n"
            "# Crear reserva (usa el token recibido)\n"
            "curl -s -X POST http://localhost:8000/api/reservations \\\n"
            "  -H 'Authorization: Bearer TOKEN' -H 'Accept: application/json' \\\n"
            "  -H 'Content-Type: application/json' \\\n"
            "  -d '{\"table_id\":1,\"date\":\"2026-12-01\",\"time\":\"20:00\",\"party_size\":2}'",
            styles["code"],
        )
    )

    story.append(Paragraph("7. Checklist de resolución", styles["h2"]))
    story.append(
        bullets(
            [
                "Migraciones y modelos en inglés con relaciones Eloquent.",
                "Factories + seeders (incluye roles, permisos y usuarios demo).",
                "Form Requests con rules + withValidator (capacidad / disponibilidad).",
                "ApiResponse + Resources.",
                "Sanctum + HasRoles + middleware + ReservationPolicy.",
                "Tests Feature: auth, permisos, reservas y mesas disponibles.",
            ],
            styles["bullet"],
        )
    )

    story.append(Spacer(1, 0.6 * cm))
    story.append(section_rule())
    story.append(
        Paragraph(
            "Fin del documento — Parte A para resolver · Parte B como referencia de implementación.",
            styles["footer"],
        )
    )

    doc.build(story)
    print(f"PDF written to {OUTPUT}")


if __name__ == "__main__":
    build_pdf()
