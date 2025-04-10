<?php
set_time_limit(7000);
error_reporting(E_ALL & ~E_WARNING); // & ~E_NOTICE
date_default_timezone_set('Atlantic/Azores');
ini_set('default_socket_timeout', 5);

snmp_set_quick_print(1);


$debug = true;

$CFG_DB_CONN = array(
    'mydrqpe'=>array(
        'host'		=>'192.168.40.75',
        'user'		=>'mydreqp',
        'pass'		=>'051$Super',
        'database'	=>'mydreqp'
    )
);

$link = mysqli_connect($CFG_DB_CONN['mydrqpe']['host'], $CFG_DB_CONN['mydrqpe']['user'], $CFG_DB_CONN['mydrqpe']['pass'], $CFG_DB_CONN['mydrqpe']['database']);

$timeout = '200';
$retries = '5';

$PrintersSNMPCorrelations = [
    'M3655idn' => array(
        'total' => ['v3',               'iso.3.6.1.4.1.1347.42.2.5.1.1.3.1'], // Todos os tipos
        'impressoes_preto' => ['v3',    'iso.3.6.1.4.1.1347.42.3.1.1.1.1.1'], // é o total das impressoes e não da cor - mas esta imprime só preto
        'copias_preto' => ['v3',        'iso.3.6.1.4.1.1347.42.3.1.1.1.1.2'], 
        'duplex_1_lado' => ['v3',       'iso.3.6.1.4.1.1347.42.2.5.1.1.1.1'], 
        'duplex_2_lados' => ['v3',      'iso.3.6.1.4.1.1347.42.2.5.1.1.2.1'], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1']
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        
    ), 
    
    // Secretaria - 153
    '2551ci' => array( 
        'total' => ['v3',               'iso.3.6.1.4.1.1347.42.2.5.1.1.3.1'], // TODO: Falta corrigir o total ... 
        'impressoes_preto' => ['v3',    'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.1'], 
        'impressoes_cor' => ['v3',      'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.3'], 
        'copias_preto' => ['v3',        'iso.3.6.1.4.1.1347.42.3.1.2.1.1.2.1'], 
        'copias_cor' => ['v3',          'iso.3.6.1.4.1.1347.42.3.1.2.1.1.2.3'], 
        'duplex_1_lado' => ['v3',       'iso.3.6.1.4.1.1347.42.2.5.1.1.1.1'], 
        'duplex_2_lados' => ['v3',      'iso.3.6.1.4.1.1347.42.2.5.1.1.2.1'], 
        'toner_ciano_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_ciano' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'], 
        'toner_magenta_total' => ['off','iso.3.6.1.2.1.43.11.1.1.8.1.2'], 
        'toner_magenta' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.2'], 
        'toner_amarelo_total' => ['off','iso.3.6.1.2.1.43.11.1.1.8.1.3'], 
        'toner_amarelo' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.3'], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.4'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.4'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    ), 
	
    // Secretaria - 151
    '3051ci' => array(
        'total' => ['v3',               'iso.3.6.1.4.1.1347.42.2.5.1.1.3.1'], 
        
		'impressoes_preto' => ['v3',    'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.1'],
        'impressoes_cor' => ['v3',      'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.3'], 
        
        'copias_preto' => ['v3',        'iso.3.6.1.4.1.1347.42.3.1.1.1.1.3'], // TODO: Falta confirmar
        //'copias_cor' => ['v3',          'iso.3.6.1.4.1.1347.42.3.1.1.1.1.2'], // TODO: Falta confirmar
		'copias_cor' => ['v3',          'iso.3.6.1.4.1.1347.42.3.1.2.1.1.2.3'],
		'duplex_1_lado' => ['v3',       'iso.3.6.1.4.1.1347.42.2.5.1.1.1.1'],
        'duplex_2_lados' => ['v3',      'iso.3.6.1.4.1.1347.42.2.5.1.1.2.1'], 
        'toner_ciano_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_ciano' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'], 
        'toner_magenta_total' => ['off','iso.3.6.1.2.1.43.11.1.1.8.1.2'], 
        'toner_magenta' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.2'], 
        'toner_amarelo_total' => ['off','iso.3.6.1.2.1.43.11.1.1.8.1.3'], 
        'toner_amarelo' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.3'], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.4'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.4'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    ), 
    
    // FRE
    '3253ci' => array(
        'total' => ['v3',               'iso.3.6.1.4.1.1347.42.2.5.1.1.3.1'], 
        'impressoes_preto' => ['v3',    'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.1'], 
        'impressoes_cor' => ['v3',      'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.3'], 
        
        'copias_preto' => ['v3',        'iso.3.6.1.4.1.1347.42.3.1.1.1.1.3'], // TODO: Falta confirmar
        'copias_cor' => ['v3',          'iso.3.6.1.4.1.1347.42.3.1.1.1.1.2'], // TODO: Falta confirmar
        
        'duplex_1_lado' => ['v3',       'iso.3.6.1.4.1.1347.42.2.5.1.1.1.1'], 
        'duplex_2_lados' => ['v3',      'iso.3.6.1.4.1.1347.42.2.5.1.1.2.1'], 
        'toner_ciano_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_ciano' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'], 
        'toner_magenta_total' => ['off','iso.3.6.1.2.1.43.11.1.1.8.1.2'], 
        'toner_magenta' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.2'], 
        'toner_amarelo_total' => ['off','iso.3.6.1.2.1.43.11.1.1.8.1.3'], 
        'toner_amarelo' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.3'], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.4'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.4'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    ), 

    // DIRETOR
    '2554ci' => array(
        'total' => ['v3',               'iso.3.6.1.4.1.1347.42.2.5.1.1.3.1'], 
        'impressoes_preto' => ['v3',    'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.1'], 
        'impressoes_cor' => ['v3',      'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.3'], 
        'copias_preto' => ['v3',        'iso.3.6.1.4.1.1347.42.3.1.1.1.1.3'],
        'copias_cor' => ['v3',          'iso.3.6.1.4.1.1347.42.3.1.1.1.1.2'],
        'duplex_1_lado' => ['v3',       'iso.3.6.1.4.1.1347.42.2.5.1.1.1.1'], 
        'duplex_2_lados' => ['v3',      'iso.3.6.1.4.1.1347.42.2.5.1.1.2.1'], 
        'toner_ciano_total' => ['v1',   'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_ciano' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'], 
        'toner_magenta_total' => ['v1', 'iso.3.6.1.2.1.43.11.1.1.8.1.2'], 
        'toner_magenta' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.2'], 
        'toner_amarelo_total' => ['v1', 'iso.3.6.1.2.1.43.11.1.1.8.1.3'], 
        'toner_amarelo' => ['v1',       'iso.3.6.1.2.1.43.11.1.1.9.1.3'], 
        'toner_preto_total' => ['v1',   'iso.3.6.1.2.1.43.11.1.1.8.1.4'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.4'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    ), 
    
    // Ricoh MP 301 IRT - 10.19.1.112 
    'MP301' => array(
        'total' => ['v1',               'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.1'], 
        'impressoes_preto' => ['v1',    'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.6'], 
        // TODO: Está isto tudo em falta 
        'impressoes_cor' => ['off',     ''], 
        'copias_preto' => ['v1',        'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.24'],
        'copias_cor' => ['off',         ''], 
        'duplex_1_lado' => ['off',      ''], // TODO: Fazer o total menos o dois lados 
        'duplex_2_lados' => ['v1',      'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.10'], 
        'toner_ciano_total' => ['off',  ''], 
        'toner_ciano' => ['off',        ''], 
        'toner_magenta_total' => ['off',''], 
        'toner_magenta' => ['off',      ''], 
        'toner_amarelo_total' => ['off',''], 
        'toner_amarelo' => ['off',      ''], 
        // TODO: Permitir valor de toner N/D 
        'toner_preto_total' => ['off',  ''], 
        'toner_preto' => ['custom',     '101.00'] 
    ),
    
    // Ricoh MP C2003 IRT - 10.19.1.48
    'MPC2003' => array(
        'total' => ['v1',               'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.1'], 
        'impressoes_preto' => ['v1',    'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.9'], 
        // TODO: Está isto tudo em falta
        'impressoes_cor' => ['off',     ''], 
        'copias_preto' => ['v1',        'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.3'], 
        'copias_cor' => ['v1',          'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.5'], // TODO: O que é isto ?????
        'duplex_1_lado' => ['off',      ''], // TODO: Fazer o total menos o dois lados 
        'duplex_2_lados' => ['v1',      'iso.3.6.1.4.1.367.3.2.1.2.19.5.1.9.10'], 
        'toner_ciano_total' => ['off',  ''], 
        'toner_ciano' => ['off',        ''], 
        'toner_magenta_total' => ['off',''], 
        'toner_magenta' => ['off',      ''], 
        'toner_amarelo_total' => ['off',''], 
        'toner_amarelo' => ['off',      ''], 
        'toner_preto_total' => ['off',  ''], 
        'toner_preto' => ['off',        ''] 
    ),
    
    /*
    snmp3_real_walk(): Fatal error: Authentication failure (incorrect password, community or key)
    
    */
    // Xerox IRAE Pico - 10.39.29.200
    'Xerox3325' => array(
        'total' => ['v1',               'iso.3.6.1.2.1.43.10.2.1.4.1.1'], 
        'impressoes_preto' => ['v1',    'iso.3.6.1.2.1.43.10.2.1.4.1.1'], // TODO: Verificar se este é o total ou só as impressões
        // TODO: Está isto tudo em falta 
        'impressoes_cor' => ['off',     ''], 
        'copias_preto' => ['off',       ''],
        'copias_cor' => ['off',         ''], 
        'duplex_1_lado' => ['off',      ''], 
        'duplex_2_lados' => ['off',     ''], 
        'toner_ciano_total' => ['off',  ''], 
        'toner_ciano' => ['off',        ''], 
        'toner_magenta_total' => ['off',''], 
        'toner_magenta' => ['off',      ''], 
        'toner_amarelo_total' => ['off',''], 
        'toner_amarelo' => ['off',      ''], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], // Capacidade (impressões) , correto ???
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    ),

    'Xerox7225' => array(
        'total' => ['v1',               'iso.3.6.1.2.1.43.10.2.1.4.1.1'], 
        
        'impressoes_preto' => ['v1',    'iso.3.6.1.2.1.43.10.2.1.4.1.1'], // TODO: Não está certo, esta impressora precisa SNMP V3, este é o totalm, necessita das impressões divididas

        // TODO: Está isto tudo em falta 
        'impressoes_cor' => ['off',     ''], 
        'copias_preto' => ['off',       ''],
        'copias_cor' => ['off',         ''], 
        'duplex_1_lado' => ['off',      ''], 
        'duplex_2_lados' => ['off',     ''], 
        'toner_ciano_total' => ['off',  ''], 
        'toner_ciano' => ['off',        ''], 
        'toner_magenta_total' => ['off',''], 
        'toner_magenta' => ['off',      ''], 
        'toner_amarelo_total' => ['off',''], 
        'toner_amarelo' => ['off',      ''], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    ),

    // Lexmark IRAE  iso.3.6.1.2.1
    'XM5163' => array(
        'total' => ['v1',               'iso.3.6.1.2.1.43.10.2.1.4.1.1'], 
        'impressoes_preto' => ['v1',    'iso.3.6.1.2.1.43.10.2.1.4.1.1'], 
        'impressoes_cor' => ['off',     'iso.3.6.1.4.1.1347.42.3.1.2.1.1.1.3'], // TODO: Check
        'copias_preto' => ['off',       'iso.3.6.1.4.1.1347.42.3.1.1.1.1.3'], // TODO: Check
        'copias_cor' => ['off',         ''], 
        'duplex_1_lado' => ['off',      ''], 
        'duplex_2_lados' => ['off',     ''], 
        'toner_ciano_total' => ['off',  ''], 
        'toner_ciano' => ['off',        ''], 
        'toner_magenta_total' => ['off',''], 
        'toner_magenta' => ['off',      ''], 
        'toner_amarelo_total' => ['off',''], 
        'toner_amarelo' => ['off',      ''], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    ),

    //'NC-480h' 
    
    //NC-8900h - MFC-L6900DW
    'MFC-L6900DW' => array(
        'total' => ['v1',               'iso.3.6.1.4.1.2435.2.3.9.4.2.1.5.5.52.1.1.3.1'], // OK
        'impressoes_preto' => ['v1',    'iso.3.6.1.4.1.2435.2.3.9.4.2.1.5.5.52.1.1.3.1'], 
        'impressoes_cor' => ['off',     ''], 
        'copias_preto' => ['off',       'iso.3.6.1.4.1.1347.42.3.1.1.1.1.3'], // TODO: Check
        'copias_cor' => ['off',         ''], 
        'duplex_1_lado' => ['off',      ''], 
        'duplex_2_lados' => ['off',     ''], 
        'toner_ciano_total' => ['off',  ''], 
        'toner_ciano' => ['off',        ''], 
        'toner_magenta_total' => ['off',''], 
        'toner_magenta' => ['off',      ''], 
        'toner_amarelo_total' => ['off',''], 
        'toner_amarelo' => ['off',      ''], 
        'toner_preto_total' => ['off',  'iso.3.6.1.2.1.43.11.1.1.8.1.1'], 
        'toner_preto' => ['v1',         'iso.3.6.1.2.1.43.11.1.1.9.1.1'] 
        //'TONER_RESIDUAL'  => ['v1', '????'] // TODO: Deposito de toner
        //["iso.3.6.1.2.1.43.11.1.1.9.1.5"] => "-3" (OK)
        //["iso.3.6.1.2.1.43.11.1.1.8.1.5"] => "-2" (_TOTAL)
    )
];

if (mysqli_connect_error()) {
	$logMessage = 'Erro MySQL: ' . mysqli_connect_error();
	die('Não foi possível ligar á base de dados');
}

$ipRanges = [];

$query_subnets = 'SELECT * FROM dbo_snmp_subredes WHERE ativo=1;';
$subnets = mysqli_query($link, $query_subnets) or die(mysqli_error($link));
while ( $row_subnets = mysqli_fetch_array($subnets, MYSQLI_ASSOC) ) 
    $ipRanges[] = $row_subnets['subrede'];

// Lista de impressoras com o iso.3.6.1.2.1.1.1.0 vazio e que são para sincronizar (era melhor fazer pelo SN)
$ipValid = ['10.19.1.34'];

//$byGetFileContent = ['10.39.86.200', '10.39.86.202'];

// Gamas IP testar
$ipRanges = ['10.39.29'];

foreach($ipRanges as $ipRange) {
    
    //TODO: Controlo durante o desenvolvimento
    //if($ipRange != "10.19.1") continue;

    for($IPDevice=1;$IPDevice<=254;$IPDevice++) {
        $ip = $ipRange . '.' . $IPDevice;
        
        // TODO: Controlo durante o desenvolvimento
        if($ip != "10.39.29.200") continue;
        //if(!in_array($ip, ["10.39.29.200"])) continue;
        
        //if($debug) 
        echo $ip . PHP_EOL;

        // Verifica se o IP está online e se tem SNMP
        $snmpObj = snmpget($ip, "public", "iso.3.6.1.2.1.1.1.0", $timeout, $retries); // .2.1.1.1.0 not safe ??? 

        var_dump($snmpObj);
        //if($ip == "10.19.1.237") {
        // TODO: Fazer qq coisa para guardar os que não tem serial... já permite null, testar pelo IP antes de guardar??? ...
        if($snmpObj!="" || in_array($ip, $ipValid)) { // TODO: Ver como fazer isto desaparecer
            $resultToSave['NAME'] = trim( snmpget($ip, "public", "iso.3.6.1.2.1.1.1.0", $timeout, $retries), '"');
            $resultToSave['SERIAL'] = trim( snmpget($ip, 'public', 'iso.3.6.1.2.1.43.5.1.1.17.1', $timeout, $retries), '"');
            
            // Dá erro se não fizer skip 
            if($resultToSave['SERIAL']=="") continue; 

            $resultToSave['LOCATION'] = trim( snmpget($ip, 'public', 'iso.3.6.1.2.1.1.6.0', $timeout, $retries), '"');
            
            // Verificar se o dispositivo já existe e atualizar ou inserir
            $query_check_device = 'SELECT * FROM dbo_snmp_dispositivos WHERE SN="' . $resultToSave['SERIAL'] . '";';
            $check_device = mysqli_query($link, $query_check_device);
            if($check_device===false) {
                echo mysqli_error($link);
                continue;
            }
            $deviceExists = (mysqli_num_rows($check_device)==0?false:1);
            $row_device = mysqli_fetch_array($check_device, MYSQLI_ASSOC);
            
            // TODO: Adicionar location e last update
            if(!$deviceExists) {
                $query = 'INSERT INTO dbo_snmp_dispositivos (SN, IP, nome, primeira_presenca) VALUES ("' . $resultToSave['SERIAL'] . '", "' . $ip . '", "' . $resultToSave['NAME'] . '", "' . date('Y-m-d') . '");';
                
                if($debug) echo $query . PHP_EOL;
                
                if(mysqli_query($link, $query)===false) {
                    echo mysqli_error($link);
                    continue;
                }
            } else {
                // TODO: Podem haver erros no Location - por causa do encoding UTF no Array SNMP ...
                //$query = 'UPDATE dbo_snmp_dispositivos SET IP="' . $ip . '", nome="' . $resultToSave['NAME'] . '", localizacao="' . $resultToSave['LOCATION'] . '", ultima_atualizacao="' . date('Y-m-d H:i:s') . '" WHERE SN="' . $resultToSave['SERIAL'] . '";';

                //var_dump(snmp3_get($ip, $row_device['SNMP_V3_security_name'], $row_device['SNMP_V3_security_level'], $row_device['SNMP_V3_auth_protocol'], $row_device['SNMP_V3_auth_passphrase'], $row_device['SNMP_V3_privacy_protocol'], $row_device['SNMP_V3_privacy_passphrase'], "iso.3.6.1.4.1.1347.41", $timeout, $retries));

                $query = 'UPDATE dbo_snmp_dispositivos SET IP="' . $ip . '", nome="' . $resultToSave['NAME'] . '", firmware="", localizacao="' . $resultToSave['LOCATION'] . '", ultima_atualizacao="' . date('Y-m-d H:i:s') . '" WHERE SN="' . $resultToSave['SERIAL'] . '";';
                $query_update2 = 'UPDATE dbo_snmp_dispositivos SET IP="' . $ip . '", nome="' . $resultToSave['NAME'] . '", firmware="", ultima_atualizacao="' . date('Y-m-d H:i:s') . '" WHERE SN="' . $resultToSave['SERIAL'] . '";';

                if($debug) echo $query . PHP_EOL;

                if(mysqli_query($link, $query)===false) 
                    mysqli_query($link, $query_update2) or die(mysqli_error($link));
            }
            
            
            if($deviceExists && boolval($row_device['SNMP_sincronizacao'])) {
                $query_check = 'SELECT * FROM dbo_snmp_leituras WHERE data_sincronizacao="' . date('Y-m-d') . '" AND SN="' . $resultToSave['SERIAL'] . '";'; 
                $check = mysqli_query($link, $query_check) or die(mysqli_error($link));
                
                $fields = $PrintersSNMPCorrelations[$row_device["MIB_template"]];
                
                $resultToDump = [];

                foreach($fields as $fieldName=>$fieldMIB) {
                    if($fieldMIB[0]!='off') {
                        if(substr($fieldName, 0, 6) == "toner_") {
                            // TODO: Controlar retorno de erro nos snmpget()
                            if($fieldMIB[0]=='v1') {
                                $resultToDump[$fieldName] = ( ( snmpget($ip, 'public', $fieldMIB[1], $timeout, $retries) / snmpget($ip, 'public', $fields[$fieldName."_total"][1], $timeout, $retries) ) * 100 );
                            } elseif($fieldMIB[0]=='v3') {
                                $resultToDump[$fieldName] = ( ( snmp3_get($ip, $row_device['SNMP_V3_security_name'], $row_device['SNMP_V3_security_level'], $row_device['SNMP_V3_auth_protocol'], $row_device['SNMP_V3_auth_passphrase'], $row_device['SNMP_V3_privacy_protocol'], $row_device['SNMP_V3_privacy_passphrase'], $fieldMIB[1], $timeout, $retries)
                                                                /
                                                                snmp3_get($ip, $row_device['SNMP_V3_security_name'], $row_device['SNMP_V3_security_level'], $row_device['SNMP_V3_auth_protocol'], $row_device['SNMP_V3_auth_passphrase'], $row_device['SNMP_V3_privacy_protocol'], $row_device['SNMP_V3_privacy_passphrase'], $fields[$fieldName."_total"][1], $timeout, $retries)
                                                            ) * 100 );
                            } else echo "Erro!";
                        } else {
                            if($fieldMIB[0]=='v1') {
                                $resultToDump[$fieldName] = snmpget($ip, 'public', $fieldMIB[1], $timeout, $retries);
                            } elseif($fieldMIB[0]=='v3') {
                                
                                //echo $row_device['SNMP_V3_security_name'] . ', ' . $row_device['SNMP_V3_security_level'] . ', ' . $row_device['SNMP_V3_auth_protocol'] . ', ' . $row_device['SNMP_V3_auth_passphrase'] . ', ' . $row_device['SNMP_V3_privacy_protocol'] . ', ' . $row_device['SNMP_V3_privacy_passphrase'];
                                $resultToDump[$fieldName] = snmp3_get($ip, $row_device['SNMP_V3_security_name'], $row_device['SNMP_V3_security_level'], $row_device['SNMP_V3_auth_protocol'], $row_device['SNMP_V3_auth_passphrase'], $row_device['SNMP_V3_privacy_protocol'], $row_device['SNMP_V3_privacy_passphrase'], $fieldMIB[1], $timeout, $retries);
                            } elseif($fieldMIB[0]=='custom') {
                                $resultToDump[$fieldName] = $fieldMIB[1];
                            } else echo "Erro!";
                        }
                    }
                }
                
                $ln = 0;
                $fieldsInsert = [];
                $fieldsUpdate = [];
                foreach($resultToDump as $fieldName=>$fieldValue) {
                    $checkIfFieldNameQuery = 'SELECT `COLUMN_NAME` AS COLUNA FROM INFORMATION_SCHEMA.COLUMNS WHERE `COLUMN_NAME`="' . $fieldName . '" AND TABLE_SCHEMA="' . $CFG_DB_CONN['mydrqpe']['database'] . '" AND TABLE_NAME="dbo_snmp_leituras";';
                    $checkIfFieldName = mysqli_query($link, $checkIfFieldNameQuery) or die(mysqli_error($link));
                    $fieldExists = (mysqli_num_rows($checkIfFieldName)==0?false:1);

                    if($fieldExists) {
                        if(mysqli_num_rows($check)==0) {
                            $fieldsInsert['campos'][$ln] = $fieldName;
                            $fieldsInsert['valores'][$ln] = $fieldValue;
                        } else {
                            $fieldsUpdate[$ln] = $fieldName . "=" . $fieldValue;
                        }
                    }

                    $ln++;
                }

                if(mysqli_num_rows($check)==0) {
                    $query = 'INSERT INTO dbo_snmp_leituras (data_sincronizacao, SN, IP, ';
                    $query .= implode(", ", $fieldsInsert['campos']);
                    $query .= ', ultima_atualizacao ) VALUES ("' . date('Y-m-d') . '", "' . $resultToSave['SERIAL'] . '", "' . $ip . '", ';
                    $query .= implode(", ", $fieldsInsert['valores']);
                    $query .= ', "' . date('Y-m-d H:i:s') . '");';
                } else {
                    $query = 'UPDATE dbo_snmp_leituras SET IP="' . $ip . '", ';
                    $query .= implode(", ", $fieldsUpdate);
                    $query .= ', ultima_atualizacao="' . date('Y-m-d H:i:s') . '" WHERE data_sincronizacao="' . date('Y-m-d') . '" AND SN="' . $resultToSave['SERIAL'] . '";';
                }

                if($debug) echo $query . PHP_EOL;

                //mysqli_query($link, $query) or die(mysqli_error($link));
                
            }
        }
    }
}

die();

?>