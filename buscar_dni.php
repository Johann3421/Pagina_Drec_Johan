<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $token1 = 'apis-token-10779.deFjdQHVSuenRlLS27jpqtmQ0SJV4hfj';  // Token API 1
  $token2 = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6Imxvcml0b3gzNDIxQGdtYWlsLmNvbSJ9.WN9y8akxDNlUsWzvwD1Nv7eJGk3qx5Gaaa6VHmjJyf4';  // Token API 2
  $dni = $_POST['dni'] ?? '';

  if (empty($dni)) {
    echo json_encode(['success' => false, 'error' => 'El DNI es obligatorio.']);
    exit();
  }

  // Primera consulta a la API
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => array(
      'Referer: https://apis.net.pe/consulta-dni-api',
      'Authorization: Bearer ' . $token1
    ),
  ));

  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);

  $persona = json_decode($response);

  // Verificar si la primera consulta fue exitosa
  if ($httpCode === 200 && !isset($persona->error)) {
    $nombre = trim($persona->nombres . " " . $persona->apellidoPaterno . " " . $persona->apellidoMaterno);
    echo json_encode(['success' => true, 'nombre' => $nombre]);
  } else {
    // Segunda consulta a la API si la primera falla
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://dniruc.apisperu.com/api/v1/dni/' . $dni . '?token=' . $token2,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => false,
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $persona = json_decode($response);

    // Verificar si la segunda consulta fue exitosa
    if ($httpCode === 200 && !isset($persona->error)) {
      $nombre = trim($persona->nombres . " " . $persona->apellidoPaterno . " " . $persona->apellidoMaterno);
      echo json_encode(['success' => true, 'nombre' => $nombre]);
    } else {
      echo json_encode(['success' => false, 'error' => 'No se encontró el DNI o ocurrió un error en ambas consultas.']);
    }
  }
}
?>
