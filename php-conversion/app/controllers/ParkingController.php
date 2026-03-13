<?php
/**
 * Parking Controller
 * Handles all parking-related API endpoints
 */

class ParkingController
{
    private $parking_model;

    public function __construct($parking_model)
    {
        $this->parking_model = $parking_model;
    }

    /**
     * GET /api/parking/:mosque_id
     */
    public function get($mosque_id)
    {
        try {
            $parking = $this->parking_model->getByMosqueId($mosque_id);

            if (!$parking) {
                return json_response(['error' => 'No parking info found'], 404);
            }

            return json_response($parking, 200);
        } catch (Exception $e) {
            return json_response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT /api/parking/:mosque_id
     */
    public function update($mosque_id)
    {
        try {
            $data = get_json_input();

            $result = $this->parking_model->updateOrInsert($mosque_id, $data);

            if ($result === 'inserted') {
                return json_response(['message' => 'Parking info inserted'], 201);
            }

            return json_response(['message' => 'Parking info updated'], 200);
        } catch (Exception $e) {
            return json_response(['error' => 'Failed to save parking info'], 500);
        }
    }
}
