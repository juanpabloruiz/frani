<?php
// phpMyAdmin: entra directo como frani (configurado vía PMA_USER en compose).
// Para entrar como root: en el selector de servidor (arriba a la izquierda)
// elegir "root" y cargar las credenciales.
$cfg['Servers'][1]['verbose'] = 'frani';

$cfg['Servers'][2]['verbose'] = 'root';
$cfg['Servers'][2]['host'] = 'db';
$cfg['Servers'][2]['port'] = 3306;
$cfg['Servers'][2]['auth_type'] = 'cookie';
$cfg['Servers'][2]['AllowNoPassword'] = false;
$cfg['Servers'][2]['compress'] = false;