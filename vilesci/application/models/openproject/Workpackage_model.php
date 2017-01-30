<?php
defined('BASEPATH') || exit('No direct script access allowed');

/*
 * OpenProject Workpackage
 */
class Workpackage_model extends FHCOP_Model
{

    /**
     * Workpackage_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Create a work package.
     *
     * Creates a work package via the Open Project API.
     *
     * @param Workpackage $wp the work package object to be persisted
     * @return int|null The id of the created work package or null if it fails.
     */
    public function create($wp)
    {
        $data = [
            'subject' => $wp->subject,
            'description' => [
                'raw' => $wp->description,
            ],
            '_links' => [
                'type' => [
                    'href' => $wp->typeResource
                ],
                'responsible' => [
                    'href' => $wp->responsibleResource
                ],
                'status' => [
                    'href' => $wp->statusResource
                ],
                'parent' => [
                    'href' => $wp->parentResource
                ],
            ],
            'startDate' => $wp->startDate,
            'dueDate' => $wp->dueDate,
            'estimatedTime' => $wp->estimatedTime,
        ];

        $result = $this->rest->postJSON("projects/$wp->projectID/work_packages", $data);

        if (!property_exists($result, 'id'))
        {
            print_r($result);
        }

        return property_exists($result, 'id') ? $result->id : null;
    }

    /**
     * Sets the parent work package of a work package
     *
     * @param int $id work package to set parent for
     * @param int $parentID parent work package
     */
    public function set_parent($id, $parentID)
    {
        $workPackage = $this->rest->get("work_packages/{$id}");

        if (!property_exists($workPackage, 'id'))
        {
            // work package does not exists
            return;
        }

        $api_path = $this->config->item('openproject')['api_path'];

        $data = [
            "lockVersion" => $workPackage->lockVersion,
            "_links" => [
                "parent" => [
                    "href" => "{$api_path}work_packages/{$parentID}"
                ]
            ]
        ];
        $this->rest->patch("work_packages/{$id}", json_encode($data), 'json');
    }
}
