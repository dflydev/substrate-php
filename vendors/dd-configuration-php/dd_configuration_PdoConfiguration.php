<?php
/**
 * PDO based Configuration.
 * @package dd_configuration
 */

require_once('dd_configuration_MapConfiguration.php');

/**
 * PDO based Configuration.
 *
 * An dd_configuration_IConfiguration implementation that is
 * populated from a PDO data source.
 *
 * @package dd_configuration
 */
class dd_configuration_PdoConfiguration extends dd_configuration_MapConfiguration {

    /**
     * Constructor.
     * @param PDO $dataSource Configuration data source.
     * @param string $tableName Table name.
     * @param string $keyColumn Column name that contains the configuration key.
     * @param string $keyValue Column name that contains the configuration value.
     */
    public function __construct(PDO $dataSource, $tableName = 'configuration', $keyColumn = 'configName', $keyValue = 'configValue') {
        $sql = 'SELECT ' . $keyColumn . ' AS configurationKey, ' . $keyValue . ' AS configurationValue FROM ' . $tableName;
        $sth = $dataSource->prepare($sql);
        $sth->execute();
        foreach ( $sth->fetchAll(PDO::FETCH_ASSOC) as $row ) {
            $this->map[$row['configurationKey']] = $row['configurationValue'];
        }
    }

}

?>
