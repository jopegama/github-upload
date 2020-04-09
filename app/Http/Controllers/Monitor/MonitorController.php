<?php

namespace App\Http\Controllers\Monitor;
use Illuminate\Support\Facades\DB;
use App\Models\Minuto;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;

class MonitorController extends Controller
{
  public function minuto(Request $request)
  {

    $my_timer = microtime();
    $time_parts = explode(' ', $my_timer);
    $time_right_now = $time_parts[1] + $time_parts[0];
    $starting_time = $time_right_now;

    $parameters = $this->rangoUTC($request);
    $view = $this->muestraPlazo($parameters);
    
    $my_timer = microtime();
    $time_parts = explode(' ', $my_timer);
    $time_right_now = $time_parts[1] + $time_parts[0];
    $finishing_time = $time_right_now;
    $total_time_in_secs = round(($finishing_time - $starting_time), 4);
    echo 'Page generated in '.$total_time_in_secs.' seconds.';
    
    return $view;
  }
  function muestraPlazo($parameters)
  {
    $startMin = $parameters['startMin'];
    $endMin   = $parameters['endMin'];
    $rangeMin = $parameters['rangeMin'];
    $deltaMin = $parameters['deltaMin'];
//    $minutos = Minuto::where('MinAbsUTC', '>=', $parameters['startMin'])->where('MinAbsUTC', '<', $parameters['endMin'])->whereRaw('MinAbsUTC % 5 = 0')->get();
    switch ($parameters['plazo']) {
        case "todo":
        case "ano":
        case "mes":
            $previousMonth = (clone $parameters['startDate'])->modify('-1 year');
            $previousMinDelta = ($parameters['startDate']->getTimestamp() - $previousMonth->getTimestamp()) / 60;
            $sql = "SELECT M.MinutoAbs as MinAbsUTC, TFuera, prevMonthTFuera
                    FROM rango_015 AS M
                    left JOIN (SELECT MinutoAbs, TFuera as prevMonthTFuera
                               FROM rango_015
                               WHERE MinutoAbs BETWEEN ($startMin - $previousMinDelta) AND ($endMin  - $previousMinDelta) and (MinutoAbs % $deltaMin = 0)
                               ) AS A ON (A.MinutoAbs + $previousMinDelta) = M.MinutoAbs
                    WHERE (M.MinutoAbs >= $startMin) AND (M.MinutoAbs <= $endMin) and (M.MinutoAbs % $deltaMin = 0)
                    ORDER BY M.MinutoAbs";
            $graficos = ['G1'=>['Grafico'=>'G1', 'curvas'=>['G1C1'=>['campo'=>'prevMonthTFuera', 'Color'=>'#00ff00','Path'=>''],
                                                      'G1C2'=>['campo'=>'TFuera',          'Color'=>'#444444','Path'=>'']
                                                     ]
                        ]
                        ];
            break;
        case "semana":
            $sql = "SELECT MinAbsUTC, TFuera, prevWeekTFuera
                    FROM minuto AS M
                    left JOIN (SELECT MinAbsUTC % (1440 * 7) AS relMinAbsUTC, TFuera as prevWeekTFuera
                               FROM minuto
                               WHERE MinAbsUTC BETWEEN $startMin - (1440 * 7) AND $endMin  - (1440 * 7) and (MinAbsUTC % $deltaMin = 0)
                               ) AS A ON A.relMinAbsUTC = M.MinAbsUTC % (1440 * 7)
                    WHERE (MinAbsUTC >= $startMin) AND (MinAbsUTC <= $endMin) and (MinAbsUTC % $deltaMin = 0)
                    ORDER BY MinAbsUTC";
            $graficos = ['G1'=>['nombre'=>'G1', 'curvas'=>['G1C1'=>['campo'=>'prevWeekTFuera', 'Color'=>'#00ff00','Path'=>''],
                                                      'G1C2'=>['campo'=>'TFuera',         'Color'=>'#444444','Path'=>'']
                                                     ]
                        ]
                        ];
            break;
        case "dia":
            $sql = "SELECT M.*, MinAbsUTC, TStamp, TuboE, TuboS, TFer, TDaniel, tACS, TFuera, minTFuera, maxTFuera, avgTFuera
                    FROM minuto AS M
                    left JOIN (SELECT min(MinAbsUTC) % 1440 AS relMinAbsUTC, min(TFuera) AS minTFuera, max(TFuera) AS maxTFuera, round(avg(TFuera),2) AS avgTFuera, MIN(TStamp) AS minTStamp, MAX(TStamp) AS maxTStamp
                               FROM minuto
                               WHERE MinAbsUTC BETWEEN $startMin - (1440 * 6) AND ($endMin + 1440 * 5) and (MinAbsUTC % $deltaMin = 0)
                               GROUP BY MinAbsUTC % 1440) AS A ON A.relMinAbsUTC = (M.MinAbsUTC % 1440)
                    WHERE (MinAbsUTC >= $startMin) AND (MinAbsUTC <= $endMin) and (MinAbsUTC % $deltaMin = 0)
                    ORDER BY MinAbsUTC";
            $graficos = ['G1'=>['nombre'=>'G1', 'curvas'=>['G1C1'=>['campo'=>'avgTFuera',  'Color'=>'#00ff00','Path'=>''],
                                                      'G1C2'=>['campo'=>'minTFuera',  'Color'=>'#0000ff','Path'=>''],
                                                      'G1C3'=>['campo'=>'maxTFuera',  'Color'=>'#ff0000','Path'=>''],
                                                      'G1C4'=>['campo'=>'TFuera',     'Color'=>'#444444','Path'=>'']
                                                     ]
                        ],
                        'G2'=>['nombre'=>'G2', 'curvas'=>['G2C1'=>['campo'=>'TuboE',  'Color'=>'#4285f4','Path'=>''],
                                                      'G2C2'=>['campo'=>'TuboS',  'Color'=>'#db4437','Path'=>''],
                                                      'G2C3'=>['campo'=>'tACS',   'Color'=>'#444444','Path'=>'']
                                                     ]
                        ],
                        'G3'=>['nombre'=>'G3', 'curvas'=>['G3C1'=>['campo'=>'TFer',   'Color'=>'#db4437','Path'=>''],
                                                      'G3C2'=>['campo'=>'TDaniel','Color'=>'#f4b400','Path'=>'']
                                                     ]
                        ]
                       ];
            break;
        default:
            $graficos = [['Grafico'=>'G1', 'curvas'=>[['Curva'=>'TuboE',  'Color'=>'#4285f4','Path'=>''],
                                                      ['Curva'=>'TuboS',  'Color'=>'#db4437','Path'=>''],
                                                      ['Curva'=>'TFer',   'Color'=>'#db4437','Path'=>''],
                                                      ['Curva'=>'TDaniel','Color'=>'#f4b400','Path'=>''],
                                                      ['Curva'=>'tACS',   'Color'=>'#444444','Path'=>'']
                                                     ]
                        ],
                        ['Grafico'=>'G2', 'curvas'=>[['Curva'=>'TuboE',  'Color'=>'#4285f4','Path'=>''],
                                                      ['Curva'=>'TuboS',  'Color'=>'#db4437','Path'=>''],
                                                      ['Curva'=>'tACS',   'Color'=>'#444444','Path'=>'']
                                                     ]
                        ],
                        ['Grafico'=>'G3', 'curvas'=>[['Curva'=>'TFer',   'Color'=>'#db4437','Path'=>''],
                                                      ['Curva'=>'TDaniel','Color'=>'#f4b400','Path'=>'']
                                                     ]
                        ]
                       ];
    }    

    $minutos = DB::select($sql);
//    echo($sql);   
                        
    return view('monitor.monitor', array('minutos' => $minutos, 'parameters' => $parameters, 'graficos' => $graficos));
  }
  function minAbsUTC($yyyy, $mm, $dd)
  {
    $originDate = new \DateTime('1999-12-31', new \DateTimeZone('UTC'));
    $newDate = new \DateTime($yyyy . "-" . $mm . "-" . $dd, new \DateTimeZone('Europe/Madrid'));
    $newDate->setTimeZone(new \DateTimeZone('UTC'));
    $minAbsUTC = ($newDate->getTimestamp() - $originDate->getTimestamp()) / 60;
    return $minAbsUTC;
  }
  function rangoUTC(Request $request)
  {
    $parameters = $request->all();
    if (count($parameters) ==0){
        $parameters = ["ano"=>now()->year, "mes"=>now()->month, "dia"=>now()->day, "plazo"=>"dia", "desplazamiento"=>"enviar"];
    }
    $tzLocal = new \DateTimeZone('Europe/Madrid');
    $tzUTC = new \DateTimeZone('UTC');
    $startDate = new \DateTime($parameters['ano'] . "-" . $parameters['mes'] . "-" . $parameters['dia'], $tzLocal);
    switch ($parameters['plazo']) {
        case "todo":
            $interval = new \DateInterval('P100Y');
            $deltaMin = 1440;
            break;
        case "ano":
            $interval = new \DateInterval('P1Y');
            $deltaMin = 1440;
            break;
        case "mes":
            $interval = new \DateInterval('P1M');
            $deltaMin = 60;
            break;
        case "semana":
            $interval = new \DateInterval('P1W');
            $deltaMin = 60;
            break;
        case "dia":
            $interval = new \DateInterval('P1D');
            $deltaMin = 15;
            break;
        default:
            $interval = new \DateInterval('P1D');
            $deltaMin = 15;
    }    
    switch ($parameters['desplazamiento']) {
        case "enviar":
//          $startDate = $startDate;
        break;
        case "anterior":
            $startDate = $startDate->sub($interval);
            break;
        case "siguiente":
            $startDate = $startDate->add($interval);
           break;
    }
    $endDate = (clone $startDate)->add($interval);

    $sql = "SELECT date(TStamp) as lastDate FROM minuto AS M WHERE MinAbsUTC = (SELECT MAX(MinAbsUTC) FROM minuto)";
    $lastDate = new \DateTime(DB::select($sql)[0]->{"lastDate"}, $tzLocal);
    $lastDate->modify('+1 day');

    if($endDate > $lastDate){
        $endDate = clone $lastDate;
        $startDate = (clone $lastDate)->sub($interval);
    }

    $originDate = new \DateTimeImmutable('1999-12-31', $tzUTC);

    $startDate->setTimeZone($tzUTC);
    $startMin = ($startDate->getTimestamp() - $originDate->getTimestamp()) / 60;
    $startDate->setTimeZone($tzLocal);

    $endDate->setTimeZone($tzUTC);
    $endMin = ($endDate->getTimestamp() - $originDate->getTimestamp()) / 60 - 1;
    $endDate->setTimeZone($tzLocal);

    $parameters['ano'] = $startDate->format("Y");
    $parameters['mes'] = $startDate->format("m");
    $parameters['dia'] = $startDate->format("d");
    $parameters['startMin']=$startMin;
    $parameters['endMin']=$endMin;
    $parameters['rangeMin']=$endMin - $startMin + 1;
    $parameters['deltaMin']=$deltaMin;    
    $parameters['startDate'] = $startDate;

    return $parameters;
  }
}

