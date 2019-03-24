<?php
namespace app\custom\Http\Controller;

use app\custom\Models\SpaceFlightDB\Crew;
use app\custom\Models\SpaceFlightDB\LaunchSites;
use app\custom\Models\SpaceFlightDB\MasterMissions;
use app\custom\Models\SpaceFlightDB\Missions;
use app\custom\Models\SpaceFlightDB\Payloads;
use app\custom\Models\SpaceFlightDB\PossibleOutcome;
use app\custom\Models\SpaceFlightDB\Post;
use app\custom\Models\SpaceFlightDB\SLSstages;
use app\custom\Models\SpaceFlightDB\SpaceLaunchSystem;
use app\custom\Models\SpaceFlightDB\Spaceman;
use app\framework\Component\StdLib\StdLibTrait;

class SpaceFlightDatabase
{
    use StdLibTrait;

    public function main()
    {
        view("SpaceFlightDatabase/main", [
            "text" => "Space Flight Database",
            "breadcrumb" => "fetch:core/breadcrumb_single",
            "numOfPayloads" => Payloads::getInstance()->numOfDataSets(),
            "numOfMissions" => Missions::getInstance()->numOfDataSets(),
            "numOfSpaceman" => Spaceman::getInstance()->numOfDataSets(),
        ]);
    }

    public function masterMissions($masterMissionID)
    {
        if($masterMissionID == null){
            $missions = MasterMissions::getInstance()->getAll(['ID', 'Name', 'StartDate']);

            foreach($missions as $key => $mission){
                $all        = Missions::getInstance()->numOfDataSetsWhere(['MasterMissionIDs' => $mission['ID'], 'StartDateTime[>]'=>null]);
                $successful = Missions::getInstance()->numOfDataSetsWhere(['MasterMissionIDs' => $mission['ID'], 'outcome' => 1, 'StartDateTime[>]' => null]);
                $failed     = Missions::getInstance()->numOfDataSetsWhere(['MasterMissionIDs' => $mission['ID'], 'outcome' => 2, 'StartDateTime[>]' => null]);
                $noData     = Missions::getInstance()->numOfDataSetsWhere(['MasterMissionIDs' => $mission['ID'], 'outcome' => null, 'StartDateTime[>]' => null]);

                $missions[$key]['outcomeStats']['all']        = '<p class="text-info">'.$all.'</p>';
                $missions[$key]['outcomeStats']['successful'] = '<p class="text-success">'.$successful.'</p>';
                $missions[$key]['outcomeStats']['failed']     = '<p class="text-danger">'.$failed.'</p>';
                $missions[$key]['outcomeStats']['noData']     = '<p class="\">'.$noData.'</p>';
            }

            view("SpaceFlightDatabase/masterMission", [
                "breadcrumb" => view("fetch:core/breadcrumb_single", [
                        "isNotEndLevel" => true,
                        "linkToLowerLevel" => "/sfdb",
                        "text" => "Space Flight DB",
                    ]).view("fetch:core/breadcrumb_single", [
                        "text" => "Master Missions",
                    ]),
                'masterMissionData' => $missions,
                'isSpecific' => false
            ]);
        } else {
            // show specific Master Mission
            $missions = Missions::getInstance()->getAllWhere(['ID', 'Name', 'StartDateTime'], ['MasterMissionIDs' => $masterMissionID, 'StartDateTime[>]' => null, 'ORDER' => ['StartDateTime' => 'ASC']]);
            $currentMasterMissionName = MasterMissions::getInstance()->getByID("Name", $masterMissionID);

            view("SpaceFlightDatabase/masterMission", [
                "breadcrumb" => view("fetch:core/breadcrumb_single", [
                        "isNotEndLevel" => true,
                        "linkToLowerLevel" => "/sfdb",
                        "text" => "Space Flight DB",
                    ]).view("fetch:core/breadcrumb_single", [
                        "isNotEndLevel" => true,
                        "linkToLowerLevel" => "/sfdb/missions/master",
                        "text" => "Master Missions",
                    ]).view("fetch:core/breadcrumb_single", [
                        "text" => $currentMasterMissionName
                    ]),
                'missionName' => $currentMasterMissionName,
                'masterMissionData' => $missions,
                'isSpecific' => true,
            ]);
        }
    }

    public function mission($ID)
    {
        if($this->isNull($ID)) {
            $numOfMissionsPerYearData = Missions::getInstance()->getAllWhere(['ID', 'StartDateTime'], ["ID[>=]" => 0, "ORDER" => ['StartDateTime'=>"ASC"]]);
            $dataForJSONArray = [];
            $temp = [];

            foreach ($numOfMissionsPerYearData as $dataSet){
                if($dataSet['StartDateTime'] !== null){
                    $dateTime = $this->datetime($dataSet['StartDateTime']);

                    if($temp[$dateTime->getYear()]){
                        $temp[$dateTime->getYear()] += 1;
                    } else {
                        $temp[$dateTime->getYear()] = 1;
                    }
                }
            }

            foreach($temp as $key => $value){
                $dataForJSONArray[] = [
                    "year" => (string)$key,
                    "value" => $value
                ];
            }

            $missionTableData = Missions::getInstance()->getAllWhere("*", ["ID[>=]" => "1", "StartDateTime[>]"=>null, "ORDER"=>['StartDateTime'=>"ASC"]]);
            foreach($missionTableData as $key => $dataSet){
                $missionTableData[$key]['outcome']             = Missions::getInstance()->getOutcomeByID($dataSet['outcome']);
                $missionTableData[$key]['SpaceLaunchSystemID'] = SpaceLaunchSystem::getInstance()->getByID(["ID","Name"], $dataSet['SpaceLaunchSystemID']);
                $missionTableData[$key]['MasterMissionIDs']    = MasterMissions::getInstance()->getByID(["ID","Name"], $dataSet['MasterMissionIDs']);
            }

            view("SpaceFlightDatabase/noMissionSpecified", [
               "breadcrumb" => view("fetch:core/breadcrumb_single", [
                       "isNotEndLevel" => true,
                       "linkToLowerLevel" => "/sfdb",
                       "text" => "Space Flight DB",
                   ]).view("fetch:core/breadcrumb_single", [
                       "text" => "Mission",
                   ]),
               "dataForStats" => json_encode($dataForJSONArray),
               "SpaceFlightMissionTableData" => $missionTableData,
            ]);
        } else {
            $missionData                        = Missions::getInstance()->getByID($ID);
            $missionData['outcome']             = Missions::getInstance()->getOutcomeByID($missionData['outcome']);
            $missionData['LaunchSiteID']        = LaunchSites::getInstance()->getByID($missionData['LaunchSiteID']);
            $missionData['SpaceLaunchSystemID'] = SpaceLaunchSystem::getInstance()->getByID($missionData['SpaceLaunchSystemID']);
            $missionData['MasterMissionIDs']    = MasterMissions::getInstance()->getByID($missionData['MasterMissionIDs']);

            $missionData['SpaceLaunchSystemID']['MaxPayloadLEO'] = number_format($missionData['SpaceLaunchSystemID']['MaxPayloadLEO'], 2, ',', '.')." kg";
            $missionData['SpaceLaunchSystemID']['MaxHeight']     = number_format($missionData['SpaceLaunchSystemID']['MaxHeight'],2, ',', '.')." m";
            $missionData['SpaceLaunchSystemID']['maxDiameter']   = number_format($missionData['SpaceLaunchSystemID']['MaxDiameter'],2, ',', '.')." m";

            view("SpaceFlightDatabase/mission", [
                "breadcrumb" => view("fetch:core/breadcrumb_single", [
                            "isNotEndLevel" => true,
                            "linkToLowerLevel" => "/sfdb",
                            "text" => "Space Flight DB",
                        ]).view("fetch:core/breadcrumb_single", [
                            "isNotEndLevel" => true,
                            "linkToLowerLevel" => "/sfdb/mission",
                            "text" => "Mission",
                        ]).view("fetch:core/breadcrumb_single", [
                            "text" => $missionData['Name']
                        ]),

                'MissionMainInfo'      => $missionData,
                'MasterMission'        => $missionData['MasterMissionIDs'],
                'payloadAndTrajectory' => $this->payloadData($missionData['PayloadIDs']),
                'LaunchSiteData'       => $missionData['LaunchSiteID'],
                'SpaceLaunchSystem'    => $missionData['SpaceLaunchSystemID'],
                'spaceman'             => $this->showSpaceman($missionData['Crew']),
            ]);
        }
    }

    public function payload($ID)
    {
        $payloadName = Payloads::getInstance()->getName($ID);

        view("blank", [
            "title" => "Payload ".$payloadName,
            "pageName" => "Payload",
            "breadcrumb" => view("fetch:core/breadcrumb_single", [
                "isNotEndLevel" => true,
                "linkToLowerLevel" => "/sfdb",
                "text" => "Space Flight DB",
            ]).view("fetch:core/breadcrumb_single", [
                "isNotEndLevel" => true,
                "linkToLowerLevel" => "/sfdb/payload",
                "text" => "Payload",
            ]).view("fetch:core/breadcrumb_single", [
                    "text" => $payloadName,
                ]),
            "pageContent" => $this->payloadData($ID)
        ]);

    }

    public function showSpacemanDetailed($ID)
    {
        $spacemanData = Spaceman::getInstance()->getByID($ID);
        $post         = Post::getInstance()->getAllWhere('ID', ['AstronautIDs' => $spacemanData['ID']]);
        $crew         = Crew::getInstance()->getAllWhere('ID', ['Post' => (int) $post[0]]);
        $missions     = Missions::getInstance()->getAllWhere(['ID', 'Name', 'StartDateTime'], ['Crew' => $crew[0]]);

        foreach ($missions as $key => $mission) {
            $TempTimeObj = $this->datetime($mission['StartDateTime']);
            $missions[$key]['timeAgo'] = $TempTimeObj->getTimeAgo();
            $missions[$key]['year'] = $TempTimeObj->getYear();
            $missions[$key]['StartDate'] = explode(" ", $TempTimeObj->val())[0];
        }

        view("SpaceFlightDatabase/spacemanDetailed", [
            "pageName" => "Spaceman",

            "title" => "Spaceman ".$spacemanData['Name'],
            "breadcrumb" => view("fetch:core/breadcrumb_single", [
                    "isNotEndLevel" => true,
                    "linkToLowerLevel" => "/sfdb",
                    "text" => "Space Flight DB",
                ]).view("fetch:core/breadcrumb_single", [
                    "isNotEndLevel" => true,
                    "linkToLowerLevel" => "/sfdb/Spaceman",
                    "text" => "Spaceman",
                ]).view("fetch:core/breadcrumb_single", [
                    "text" => $spacemanData['Name'],
                ]),

            'name'        => $spacemanData['Name'],
            'birth'       => $spacemanData['Birth'],
            'death'       => $spacemanData['Death'],
            'nationality' => $spacemanData['Nationality'],
            'eva'         => $spacemanData['EVAs'],
            'training'    => $spacemanData['Training'],
            'job'         => $spacemanData['Job'],

            'missions' => $missions
        ]);
    }

    public function hangar()
    {
        $spaceLaunchSystemData = SpaceLaunchSystem::getInstance()->getAllWhere(['ID','Name', 'Manufacturer'], ['ID[>=]' => 0]);
        $htmlForOutput = "";

        foreach ($spaceLaunchSystemData as $key => $dataEntity) {
            $spaceLaunchSystemData[$key]['additionalData'] = Missions::getInstance()->getAllWhere(['ID','Name', 'outcome'], ['SpaceLaunchSystemID' => $dataEntity['ID']]);
        }

        foreach ($spaceLaunchSystemData as $dataEntity) {
            $htmlForOutput .= "<tr>";
            $htmlForOutput .= "<td>".$dataEntity['ID']."</td>";
            $htmlForOutput .= "<td><a href='/sfdb/hangar/".$dataEntity['ID']."'>".$dataEntity['Name']."</a></td>";
            $htmlForOutput .= "<td>".$dataEntity['Manufacturer']."</td>";
            $htmlForOutput .= "<td>".count($dataEntity['additionalData'])."</td>";

            $successful = 0;
            $failed     = 0;
            $else       = 0;

            foreach ($dataEntity['additionalData'] as $additionalDataSet) {
                switch ($additionalDataSet['outcome']) {
                    case 1:
                        $successful++;
                        break;

                    case 2:
                        $failed++;
                        break;

                    default:
                        $else++;
                        break;
                }
            }

            $htmlForOutput .= "<td>".$successful."</td>";
            $htmlForOutput .= "<td>".$failed."</td>";
            $htmlForOutput .= "<td>".$else."</td>";
            $htmlForOutput .= "</tr>";
        }

        view("SpaceFlightDatabase/hangar",[
            "pageName" => "Hangar",

            "title" => "",
            "breadcrumb" => view("fetch:core/breadcrumb_single", [
                "isNotEndLevel" => true,
                "linkToLowerLevel" => "/sfdb",
                "text" => "Space Flight DB",
            ]).view("fetch:core/breadcrumb_single", [
                "text" => "Hangar",
            ]),

            'SpaceFlightVehicleTableData' => $htmlForOutput,
        ]);
    }

    public function spaceLaunchSystem($ID)
    {
        $missionsData   = Missions::getInstance()->getAllWhere(['ID', 'Name', 'StartDateTime', 'LaunchSiteID', 'PayloadIDs', 'outcome'], ['SpaceLaunchSystemID' => $ID, 'ORDER' => ['StartDateTime' => 'ASC']]);
        $launchSysSpecs = SpaceLaunchSystem::getInstance()->getByID($ID, ['Name', 'StageIDs']);
        $launchSysSpecs['StageIDs'] = explode(',', $launchSysSpecs['StageIDs']);

        foreach($launchSysSpecs['StageIDs'] as $key => $stageID) {
            if(explode('x', $launchSysSpecs['StageIDs'][$key])[1] != null) {
                $launchSysSpecs['StageIDs'][$key] = explode('x', $launchSysSpecs['StageIDs'][$key]);
            }

            if(is_array($launchSysSpecs['StageIDs'][$key])) {
                $launchSysSpecs['StageIDs'][$key] = $launchSysSpecs['StageIDs'][$key][0]." x ".SLSstages::getInstance()->getByID($launchSysSpecs['StageIDs'][$key][1], 'Type');
            } else {
                $launchSysSpecs['StageIDs'][$key] = SLSstages::getInstance()->getByID($stageID, 'Type');
            }
        }

        foreach($missionsData as $key => $dataSet){
            $missionsData[$key]['outcome'] = PossibleOutcome::getInstance()->getByID($dataSet['outcome'], 'Name');
        }

        //TODO: look in smarty doc for loop iterations and use them instead of the $key
        view("SpaceFlightDatabase/spaceLaunchSys.tpl", [
            "breadcrumb" => view("fetch:core/breadcrumb_single", [
                "isNotEndLevel" => true,
                "linkToLowerLevel" => "/sfdb",
                "text" => "Space Flight DB",
            ]).view("fetch:core/breadcrumb_single", [
                "isNotEndLevel" => true,
                "linkToLowerLevel" => "/sfdb/hangar",
                "text" => "Hangar",
            ]).view("fetch:core/breadcrumb_single", [
                "text" => $launchSysSpecs['Name'],
            ]),
            "launchSysSpecs" => $launchSysSpecs,
            "SpaceFlightVehicleTableData" => $missionsData,
        ]);
    }

    public function sorted($by = null, $param = null)
    {
        switch ($by){
            case "year":
                if (!$this->isNull($param)) {
                    $data = Missions::getInstance()->getAllWhere(['ID', 'Name', 'StartDateTime', 'EndDateTime', 'outcome'], ['StartDateTime[~]' => $param, "ORDER" => ["StartDateTime" => "ASC"]]);

                    foreach ($data as $key => $value) {
                        $data[$key]['outcome'] = PossibleOutcome::getInstance()->getByID($data[$key]['outcome'], 'Name');
                    }
                }

                view("SpaceFlightDatabase/sorted/byYear", [
                    "breadcrumb" => view("fetch:core/breadcrumb_single", [
                            "isNotEndLevel" => true,
                            "linkToLowerLevel" => "/sfdb",
                            "text" => "Space Flight DB",
                        ]).view("fetch:core/breadcrumb_single", [
                            "isNotEndLevel" => true,
                            "linkToLowerLevel" => "/sfdb/sorted",
                            "text" => "Sorted",
                        ]).view("fetch:core/breadcrumb_single", [
                            "isNotEndLevel" => true,
                            "linkToLowerLevel" => "/sfdb/sorted/byYear",
                            "text" => "by Year",
                        ]),
                    'dataSets' => $data
                ]);
                break;
        }
    }

    private function payloadData($IDs)
    {
        $temp   = explode(",",$IDs);

        $result = Payloads::getInstance()->getAllByIDs($temp, '*');
        $output = "";

        foreach($result as $payload) {
            $missionEvents = Payloads::getInstance()->getMissionEvents($payload);
            Payloads::getInstance()->fullFillData($payload);

            $payload['Mass'] = number_format($payload['Mass'], 2, ',', '.') . " kg";

            if($_GET['edit'] != 'payload') {
                $output .= view('fetch:SpaceFlightDatabase/payload', [
                    'payloadData'    => $payload,
                    'missionEvents'  => $this->getMissionEvents($missionEvents)
                ]);
            } else {
                echo "edit";
            }
        }

        return $output;
    }

    private function getMissionEvents($data)
    {
        $output = '';

        foreach($data as $key => $mission){

            if($key % 3 == 0){
                if($key == 3){
                    $output .= '</div>';
                }
                $output .= '<div class="row">';
            }

            $mission['FromSpacecraftName'] = Payloads::getInstance()->getName($mission['FromSpacecraftID']);
            $mission['Periapsis']          = number_format($mission['Periapsis'], 2, ',', '.')." km";
            $mission['Apoapsis']           = number_format($mission['Apoapsis'],2, ',', '.')." km";

            $output .= view("fetch:SpaceFlightDatabase/missionEvent", [
                'missionEvent'     => $mission
            ]);
        }

        return $output;
    }

    private function showSpaceman($ID)
    {
        $crew         = Crew::getInstance()->getByID($ID);
        $crew['Post'] = Post::getInstance()->getAllByIDs(explode(",", $crew['Post']), '*');
        $output   = '';

        foreach ($crew['Post'] as $key => $post) {
            $crew['Post'][$key]['AstronautIDs'] = Spaceman::getInstance()->getAllByIDs(explode(",", $post['AstronautIDs']), '*');
        }

        foreach($crew['Post'] as $post) {
            $output .= view("fetch:SpaceFlightDatabase/post", [
                'ID'       => $post['ID'],
                'postName' => $post['Name'],
                'spacemans' => $post['AstronautIDs']
            ]);
        }

        return $output;
    }
}
