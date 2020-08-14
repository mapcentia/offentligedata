<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2020 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

namespace app\extensions\offentligedata\api;

use app\inc\Controller;
use app\inc\Input;
use app\models\Sql;


class Request extends Controller
{
    public function post_index(): array
    {
        $parsedBody = json_decode(Input::getBody(), true);

        // Check if session id from body is active
        session_id($parsedBody["session_id"]);
        session_start();
        if (empty($_SESSION["auth"])) {
            $response['success'] = false;
            $response['message'] = "No session";
            return $response;
        }

        // Get values from body
        $komKode = $parsedBody["komkode"];
        $startDate = $parsedBody["startdate"];
        $endDate = $parsedBody["enddate"];

        // Build SQL SELECT
        $url = "SELECT * FROM cvr.flyt_fad_dev({$komKode},'{$startDate}','{$endDate}')";

        // Use the SQL API to run the SELECT and get GeoJSON in return
        $sqlObj = new Sql("4326");
        $response = $sqlObj->sql($url, 'utf8'); // This is a PHP array

        return $response; // GC2 framework will transform the array to json
    }
}