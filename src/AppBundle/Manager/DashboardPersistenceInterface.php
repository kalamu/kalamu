<?php

namespace AppBundle\Manager;

/**
 * Interface for Dashboard persistence
 */
interface DashboardPersistenceInterface
{

    /**
     * Get the list of all dashboards identifiers
     */
    public function getAll();

    /**
     * Get parameters of the dashboard in JSON format
     *
     * @param string $identifier
     * @return string
     */
    public function get($identifier);

    /**
     * Set a dashboard
     *
     * @param string $identifier
     * @param string $parameters parameters of the dashboard in JSON format
     */
    public function set($identifier, $parameters);

    /**
     * Delete an dashboard
     *
     * @param string $identifier
     */
    public function remove($identifier);

}
