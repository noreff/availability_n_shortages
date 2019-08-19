<?php

namespace App\Helpers;

use \DateTime;
use \Exception;
use \PDO;

class EquipmentAvailabilityHelper {

    /**
     * @var PDO
     */
    protected $db;

    /**
     * EquipmentAvailabilityHelper constructor.
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
	 * This function checks if a given quantity is available in the passed time frame
	 * @param int      $equipment_id Id of the equipment item
	 * @param int      $quantity How much should be available
	 * @param DateTime $start Start of time window
	 * @param DateTime $end End of time window
     * @throws Exception If there is no equipment with given id
	 * @return bool True if available, false otherwise
	 */
	public function isAvailable(int $equipment_id, int $quantity, DateTime $start, DateTime $end) : bool
    {
        $sql = 'SELECT name, stock, SUM(p.quantity) as totalPlanned 
                FROM planning p
                JOIN equipment e ON p.equipment = e.id
                WHERE e.id = :equipment_id
                AND p.start >= :start
                AND p.end <= :end';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':equipment_id' => $equipment_id,
            ':start' => $start->format('Y-m-d 00:00:00'),
            ':end' => $end->format('Y-m-d 00:00:00')
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new Exception("There is no equipment with id {$equipment_id}");
        }

        return ($result['stock'] - $result['totalPlanned']) >= $quantity;
	}

	/**
	 * Calculate all items that are short in the given period
	 * @param DateTime $start Start of time window
	 * @param DateTime $end End of time window
	 * @return array Key/valyue array with as indices the equipment id's and as values the shortages
	 */
	public function getShortages(DateTime $start, DateTime $end) : array
    {
        $sql = 'SELECT e.id, (e.stock - SUM(p.quantity)) as shortage 
                FROM planning p
                JOIN equipment e ON p.equipment = e.id
                AND p.start >= :start
                AND p.end <= :end
                GROUP BY e.id
                HAVING SUM(p.quantity) > e.stock';

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':start' => $start->format('Y-m-d 00:00:00'),
            ':end' => $end->format('Y-m-d 00:00:00')
        ]);

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR );
	}

}
