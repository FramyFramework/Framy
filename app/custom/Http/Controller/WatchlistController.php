<?php

namespace app\custom\Http\Controller;

use app\custom\Models\Watchlist;

class WatchlistController
{
    public function overview()
    {
        $listData = Watchlist::getInstance()->getAllByUserID();

        $temp = $listData;
        foreach($listData as $key => $listEntity) {
            $last = $listData[$key - 1];
            if($listEntity['SeriesName'] == $last['SeriesName']) {
                if($listEntity['Session'] >= $last['Session']) {
                    unset($temp[$key]);
                }
            }
        }
        $listData = $temp;

        view("watchlist/watchlist", [
            "breadcrumb" => view("fetch:core/breadcrumb_single", [
                    "text" => "Watchlist",
                ]),
            'list' => $listData
        ]);
    }

    public function details($title)
    {
        view('watchlist/details', [
            "breadcrumb" => view("fetch:core/breadcrumb_single", [
                    "isNotEndLevel" => true,
                    "linkToLowerLevel" => "/watchlist",
                    "text" => "Watchlist",
                ]).view("fetch:core/breadcrumb_single", [
                    "text" => $title,
                ]),
            'Name' => $title,
            'list' => Watchlist::getInstance()->getBySeriesName($title)
        ]);
    }
}
