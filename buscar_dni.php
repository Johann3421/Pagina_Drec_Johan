<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $token = 'apis-token-10779.deFjdQHVSuenRlLS27jpqtmQ0SJV4hfj';  // Token API
  $dni = $_POST['dni'] ?? '';

  if (empty($dni)) {
    echo json_encode(['success' => false, 'error' => 'El DNI es obligatorio.']);
    exit();
  }

  // Iniciar llamada a la API
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => array(
      'Referer: https://apis.net.pe/consulta-dni-api',
      'Authorization: Bearer ' . $token
    ),
  ));

  $response = curl_exec($curl);
  $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  curl_close($curl);

  // Decodificar la respuesta de la API
  $persona = json_decode($response);

  // Verificar si la llamada fue exitosa
  if ($httpCode === 200 && !isset($persona->error)) {
    $nombre = trim($persona->nombres . " " . $persona->apellidoPaterno . " " . $persona->apellidoMaterno);
    echo json_encode(['success' => true, 'nombre' => $nombre]);
  } else {
    echo json_encode(['success' => false, 'error' => 'No se encontró el DNI o ocurrió un error en la consulta.']);
  }
}
?>
