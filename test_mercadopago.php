<?php
// Archivo: test_mercadopago.php
// Coloca este archivo en la raíz de tu proyecto Laravel y ejecuta: php test_mercadopago.php

require_once 'vendor/autoload.php';

use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

// Cargar configuración desde .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== TEST MERCADOPAGO ===\n";

// Verificar que existe el token (usando el nombre correcto de tu .env)
$token = $_ENV['MERCADO_PAGO_ACCESS_TOKEN'] ?? null;

if (!$token) {
    echo "❌ ERROR: No se encontró MERCADO_PAGO_ACCESS_TOKEN en el archivo .env\n";
    echo "Tu .env debería tener:\n";
    echo "MERCADO_PAGO_ACCESS_TOKEN=TEST-5940637777141783-102712-446c6b38412e311e6fa87afcd8bd8a19-2056600684\n";
    exit(1);
}

echo "✅ Token encontrado: " . substr($token, 0, 30) . "...\n";

try {
    // Configurar MercadoPago
    MercadoPagoConfig::setAccessToken($token);
    echo "✅ Token configurado correctamente\n";
    
    // Crear cliente
    $client = new PreferenceClient();
    echo "✅ Cliente creado correctamente\n";
    
    // Datos de prueba corregidos - estructura simplificada y válida
    $testData = [
        "items" => [
            [
                "title" => "Producto de prueba",
                "description" => "Producto para probar MercadoPago",
                "quantity" => 1,
                "currency_id" => "PEN",
                "unit_price" => 10.50
            ]
        ],
        "back_urls" => [
            "success" => "http://localhost:8000/orden/success/123",
            "failure" => "http://localhost:8000/orden/failed",
            "pending" => "http://localhost:8000/orden/pending"
        ],
        "auto_return" => "approved",
        "external_reference" => "test-orden-123",
        "statement_descriptor" => "ECOBAZAR"
    ];
    
    echo "📝 Datos de prueba preparados:\n";
    echo json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    // Crear preferencia
    echo "🚀 Creando preferencia...\n";
    $preference = $client->create($testData);
    
    echo "✅ ¡Preferencia creada exitosamente!\n";
    echo "ID: " . $preference->id . "\n";
    echo "Init Point: " . $preference->init_point . "\n";
    echo "Sandbox Init Point: " . ($preference->sandbox_init_point ?? 'No disponible') . "\n";
    
    echo "\n🎉 MercadoPago está funcionando correctamente!\n";
    echo "Puedes abrir este enlace para probar el pago:\n";
    echo $preference->init_point . "\n";
    
} catch (MPApiException $e) {
    echo "❌ Error de MercadoPago API:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Status Code: " . $e->getApiResponse()->getStatusCode() . "\n";
    
    try {
        $content = $e->getApiResponse()->getContent();
        if (is_array($content)) {
            echo "Respuesta detallada:\n";
            echo json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            
            // Analizar errores específicos
            if (isset($content['message'])) {
                echo "\n🔍 Análisis del error:\n";
                echo "- Mensaje: " . $content['message'] . "\n";
            }
            
            if (isset($content['cause'])) {
                echo "- Causa: " . json_encode($content['cause'], JSON_PRETTY_PRINT) . "\n";
            }
            
            if (isset($content['error'])) {
                echo "- Código de error: " . $content['error'] . "\n";
            }
        }
    } catch (Exception $ex) {
        echo "Error obteniendo detalles: " . $ex->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . " línea " . $e->getLine() . "\n";
}

echo "\n=== PRUEBA ALTERNATIVA (sin auto_return) ===\n";

// Si el primer intento falla, probemos sin auto_return
try {
    $testDataSimple = [
        "items" => [
            [
                "title" => "Producto de prueba simple",
                "quantity" => 1,
                "currency_id" => "PEN",
                "unit_price" => 15.00
            ]
        ],
        "back_urls" => [
            "success" => "http://localhost:8000/orden/success",
            "failure" => "http://localhost:8000/orden/failed",
            "pending" => "http://localhost:8000/orden/pending"
        ],
        "external_reference" => "test-simple-" . time()
    ];
    
    echo "🚀 Creando preferencia simple (sin auto_return)...\n";
    $preference2 = $client->create($testDataSimple);
    
    echo "✅ ¡Preferencia simple creada exitosamente!\n";
    echo "ID: " . $preference2->id . "\n";
    echo "Init Point: " . $preference2->init_point . "\n";
    
} catch (Exception $e) {
    echo "❌ Error en prueba simple: " . $e->getMessage() . "\n";
}

echo "\n=== FIN TEST ===\n";