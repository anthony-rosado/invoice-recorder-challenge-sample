<!DOCTYPE html>
<html>

<head>
    <title>Comprobantes Subidos</title>
</head>

<body>
    <h1>Estimado {{ $user->name }},</h1>

    @if (count($comprobantes) > 0)
    <p>Hemos recibido tus comprobantes con los siguientes detalles:</p>
    @foreach ($comprobantes as $comprobante)
    <ul>
        <li>Nombre del Emisor: {{ $comprobante->issuer_name }}</li>
        <li>Tipo de Documento del Emisor: {{ $comprobante->issuer_document_type }}</li>
        <li>Número de Documento del Emisor: {{ $comprobante->issuer_document_number }}</li>
        <li>Nombre del Receptor: {{ $comprobante->receiver_name }}</li>
        <li>Tipo de Documento del Receptor: {{ $comprobante->receiver_document_type }}</li>
        <li>Número de Documento del Receptor: {{ $comprobante->receiver_document_number }}</li>
        <li>Moneda: {{ $comprobante->currency }}</li>
        <li>Monto Total: {{ $comprobante->total_amount }}</li>
    </ul>
    @endforeach
    @endif
    @if (count($errors) > 0)
    <p>Lamentamos comunicarte que algunos archivos no pudieron procesarse correctamente:</p>
    @foreach ($errors as $error)
    <ul>
        <li>Archivo del comprobante: {{ $error['fileName'] }}</li>
        <li>Razón: {{ $error['errorReason'] }}</li>
    </ul>
    @endforeach
    @endif
    <p>¡Gracias por usar nuestro servicio!</p>
</body>

</html>