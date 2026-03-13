<?php
/**
 * Mosque Controller
 * Handles all mosque-related API endpoints
 */

class MosqueController
{
    private $mosque_model;

    public function __construct($mosque_model)
    {
        $this->mosque_model = $mosque_model;
    }

    /**
     * GET /api/mosques
     */
    public function getAll()
    {
        try {
            $mosques = $this->mosque_model->getAll();
            return json_response($mosques, 200);
        } catch (Exception $e) {
            return json_response(['error' => 'Internal server error while fetching mosques.'], 500);
        }
    }

    /**
     * GET /api/mosques/:id
     */
    public function getById($id)
    {
        try {
            $mosque = $this->mosque_model->getWithPrayerTimes($id);

            if (!$mosque) {
                return json_response(['error' => 'Mosque not found'], 404);
            }

            return json_response($mosque, 200);
        } catch (Exception $e) {
            return json_response(['error' => 'Failed to fetch mosque.'], 500);
        }
    }

    /**
     * PUT /api/mosques/:id
     */
    public function update($id)
    {
        try {
            $data = get_json_input();

            if (empty($data['name']) || empty($data['address'])) {
                return json_response(['error' => 'Name and address are required'], 400);
            }

            $name = $data['name'];
            $address = $data['address'];
            $location_url = $data['location_url'] ?? '';

            $result = $this->mosque_model->update($id, $name, $address, $location_url);

            if ($result === 0) {
                return json_response(['error' => 'Mosque not found or no changes made.'], 404);
            }

            return json_response(['message' => 'Mosque updated successfully'], 200);
        } catch (Exception $e) {
            return json_response(['error' => 'Failed to update mosque.'], 500);
        }
    }
}
