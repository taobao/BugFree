<?php
/**
 * 安装配置参数文件
 * 
 * @return $arr 配置参数数组
 */
return array(
    'version' => '3.0.4',
    'v2Table' => 'TestOptions',
    'versionMap' => array(
        8 => '2.0.4',
        9 => '2.1',
        10 => '2.1',
        11 => '2.1',
        12 => '2.1',
        13 => '2.1.1',
        14 => '2.1.2',
        15 => '2.1.3',
        16 => '3.0.0',
        17 => '3.0.1',
        18 => '3.0.2',
        19 => '3.0.3',
        20 => '3.0.4'
    ),
    'upgradeMap' => array(
        8 => 'Up2042to21',
        9 => 'Up21to2101',
        10 => 'Up2101to2102',
        11 => 'Up2102to2103',
        12 => 'Up2103to211',
        13 => 'Up211to212',
        14 => 'Up212to213',
        15 => 'Up213to3',
        16 => 'Up300to301',
        17 => 'Up301to302',
        18 => 'Up302to303',
        19 => 'Up303to304'
    ),
    'upgradeMsg' => array(
        8 => 'Upgrading v2.0.4.2 to v2.1 ...',
        9 => 'Upgrading v2.1 to v2.1.0.1 ...',
        10 => 'Upgrading v2.1.0.1 to v2.1.0.2 ...',
        11 => 'Upgrading v2.1.0.2 to v2.1.0.3 ...',
        12 => 'Upgrading v2.1.0.3 to v2.1.1 ...',
        13 => 'Upgrading v2.1.1 to v2.1.2 ...',
        14 => 'Upgrading v2.1.2 to v2.1.3 ...',
        15 => 'Upgrading v2.1.3 to v3.0.0 ...',
        16 => 'Upgrading v3.0.0 to v3.0.1 ...',
        17 => 'Upgrading v3.0.1 to v3.0.2 ...',
        18 => 'Upgrading v3.0.2 to v3.0.3 ...',
        19 => 'Upgrading v3.0.3 to v3.0.4 ...'
    )
);
?>
