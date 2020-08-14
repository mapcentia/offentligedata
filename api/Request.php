<?php
/**
 * @author     Martin Høgh <mh@mapcentia.com>
 * @copyright  2013-2020 MapCentia ApS
 * @license    http://www.gnu.org/licenses/#AGPL  GNU AFFERO GENERAL PUBLIC LICENSE 3
 *
 */

/**
 * @OA\Info(
 *   title="Offentligedata API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="mh@mapcentia.com"
 *   )
 * )
 */

namespace app\extensions\offentligedata\api;

use app\inc\Controller;
use app\inc\Input;
use app\models\Sql;


class Request extends Controller
{
    /**
     * @return array
     *
     * @OA\Post(
     *   path="/extensions/offentligedata/api/request",
     *   tags={"Offentligedata"},
     *   summary="Henter offentlige data ",
     *   @OA\RequestBody(
     *     description="Parametre med session_id og værdier til SQL'en ",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         type="object",
     *         required={"session_id","komkode","startdate","enddate"},
     *         @OA\Property(property="session_id",type="string",example="9krf6cujiqgivnlm6uk2p1hglh"),
     *         @OA\Property(property="komkode",type="string",example="420"),
     *         @OA\Property(property="startdate",type="string",example="2020-07-28"),
     *         @OA\Property(property="enddate",type="string",example="2020-07-31")
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Operation status"
     *   )
     * )
     */
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