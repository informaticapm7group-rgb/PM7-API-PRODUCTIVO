<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Nueva solicitud de contacto</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family:'Segoe UI', Roboto, Arial, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:32px 16px;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

<tr>
<td style="background-color:#25325c; padding:28px 32px;">
<p style="margin:0; color:#ffffff; font-size:20px; font-weight:700;">Nueva solicitud de contacto</p>
<p style="margin:4px 0 0; color:#a9b6d6; font-size:13px;">Formulario web — pm7group.net/contacto</p>
<p style="margin:8px 0 0; color:#ffffff; font-size:13px; font-weight:600;">N.º de seguimiento: {{ $lead['tracking_number'] }}</p>
</td>
</tr>

<tr>
<td style="padding:32px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; width:120px; vertical-align:top;">Nombre</td>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#1e293b; font-size:15px;">{{ $lead['name'] }}</td>
</tr>
<tr>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; vertical-align:top;">RUT</td>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#1e293b; font-size:15px;">{{ $lead['rut'] }}</td>
</tr>
<tr>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; vertical-align:top;">Correo</td>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; font-size:15px;"><a href="mailto:{{ $lead['email'] }}" style="color:#2047c1; text-decoration:none;">{{ $lead['email'] }}</a></td>
</tr>
<tr>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; vertical-align:top;">Teléfono</td>
<td style="padding:10px 0; border-bottom:1px solid #e2e8f0; color:#1e293b; font-size:15px;">{{ $lead['phone'] ?: '—' }}</td>
</tr>
<tr>
<td style="padding:10px 0; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; vertical-align:top;">Empresa</td>
<td style="padding:10px 0; color:#1e293b; font-size:15px;">{{ $lead['company'] ?: '—' }}</td>
</tr>
</table>

<div style="margin-top:24px; padding:20px; background-color:#f8fafc; border-radius:12px; border-left:3px solid #2047c1;">
<p style="margin:0 0 8px; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Mensaje</p>
<p style="margin:0; color:#1e293b; font-size:15px; line-height:1.6; white-space:pre-line;">{{ $lead['message'] }}</p>
</div>

<div style="margin-top:28px; text-align:center;">
<a href="mailto:{{ $lead['email'] }}" style="display:inline-block; background-color:#25325c; color:#ffffff; text-decoration:none; font-size:14px; font-weight:600; padding:12px 28px; border-radius:999px;">Responder a {{ $lead['name'] }}</a>
</div>
</td>
</tr>

<tr>
<td style="padding:20px 32px; background-color:#f8fafc; text-align:center;">
<p style="margin:0; color:#94a3b8; font-size:12px;">PM7 Group — enviado automáticamente desde el formulario de contacto del sitio web.</p>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>
