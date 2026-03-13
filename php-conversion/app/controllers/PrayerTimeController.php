<?php
/**
 * PrayerTime Controller
 * Handles all prayer time-related API endpoints
 */

class PrayerTimeController
{
    private $prayer_time_model;

    public function __construct($prayer_time_model)
    {
        $this->prayer_time_model = $prayer_time_model;
    }

    /**
     * GET /api/prayertimes/:date
     */
    public function getByDate($date)
    {
        try {
            if (empty($date)) {
                return json_response(['error' => 'Date parameter is missing'], 400);
            }

            $prayer_times = $this->prayer_time_model->getByDate($date);

            if (empty($prayer_times)) {
                return json_response(['message' => 'No prayer times found for the specified date'], 404);
            }

            return json_response($prayer_times, 200);
        } catch (Exception $e) {
            return json_response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/prayertimes/upload
     */
    public function upload()
    {
        try {
            $data = get_json_input();

            if (empty($data['prayerTimes']) || !is_array($data['prayerTimes'])) {
                return json_response(['message' => 'No prayer times provided.'], 400);
            }

            $count = 0;
            foreach ($data['prayerTimes'] as $prayer) {
                // Extract date only if full ISO datetime provided
                $date = isset($prayer['date']) ? explode('T', $prayer['date'])[0] : $prayer['date'];
                $prayer['date'] = $date;
                $this->prayer_time_model->insertOrUpdate($prayer);
                $count++;
            }

            return json_response([
                'message' => "Successfully uploaded $count prayer times",
                'count' => $count
            ], 200);
        } catch (Exception $e) {
            return json_response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * GET /api/prayertimes/by-mosque/:mosque_id?month=M&year=Y
     */
    public function getByMosqueAndMonth($mosque_id, $month, $year)
    {
        try {
            if (empty($mosque_id) || empty($month) || empty($year)) {
                return json_response(['message' => 'Missing mosqueId, month, or year'], 400);
            }

            $prayer_times = $this->prayer_time_model->getByMosqueAndMonth($mosque_id, $month, $year);

            if (empty($prayer_times)) {
                return json_response(['message' => 'No prayer times found for the specified date'], 404);
            }

            return json_response($prayer_times, 200);
        } catch (Exception $e) {
            return json_response(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PUT /api/prayertimes/:id
     */
    public function update($id)
    {
        try {
            $data = get_json_input();

            $result = $this->prayer_time_model->update($id, $data);

            if ($result === 0) {
                return json_response(['message' => 'Prayer time not found'], 404);
            }

            return json_response(['message' => 'Prayer time updated successfully'], 200);
        } catch (Exception $e) {
            return json_response(['error' => $e->getMessage()], 500);
        }
    }
}
