<html>
    <head>
    <title>Gráfico temperatura</title>
    </head>
    <body>
        <h1>Laravel Minute Demo</h1>
        <form id="formulario" action="monitor" method="get">
            <fieldset>
                <legend>Introduce fecha</legend>
                    <label>Ano</label>
                        <input id="campo1" name="ano" type="text" value={{$parameters['ano']}} />
                    <label>Mes</label>
                        <input id="campo2" name="mes" type="text"  value={{$parameters['mes']}} />
                    <label>Dia</label>
                        <input id="campo2" name="dia" type="text"  value={{$parameters['dia']}} />
                    <input type="radio" name="plazo" value="todo" {{$parameters['plazo']=='todo'?'checked':''}}> Todo<br>
                    <input type="radio" name="plazo" value="ano" {{$parameters['plazo']=='ano'?'checked':''}}> Año<br>
                    <input type="radio" name="plazo" value="mes" {{$parameters['plazo']=='mes'?'checked':''}}> Mes<br>
                    <input type="radio" name="plazo" value="semana" {{$parameters['plazo']=='semana'?'checked':''}}> Semana<br>
                    <input type="radio" name="plazo" value="dia" {{$parameters['plazo']=='dia'?'checked':''}}> Día<br>
                    <input id="campo3" name="desplazamiento" type="submit" value="enviar" />
                    <input id="campo4" name="desplazamiento" type="submit" value="anterior" />
                    <input id="campo5" name="desplazamiento" type="submit" value="siguiente" />
            </fieldset>
        </form>
        @php 
//            var_dump($parameters);
            $width = 1800;
            $deltaXInicial = 30;

//            var_export($minutos);
//            var_dump($graficos);
//            var_dump($minuto->MinAbsUTC);
//            var_dump($minuto->{$curva['campo']});
//            print_r($minutos);
//            dump($parameters);
//            dump($minutos);
//            dd($graficos);            
//            var_dump($minuto->MinAbsUTC);
//            var_dump($minuto->{$curva['campo']});
//                      $curva['Path'] .= $sep . ($minuto->MinAbsUTC - $parameters['startMin'] + 30) . ' ' . (600 - ($minuto->{$curva['campo']} - 10) * 10);      // Funciona con DB que devuelve un array de stdClass
//                        $curva['Path'] .= $sep . ($minuto['MinAbsUTC'] - $parameters['startMin'] + 30) . ' ' . (600 - ($minuto[$curva['campo']] - 10) * 10);      // Funciona con eloquent que devuelve una Collection Eloquent

            $deltaX = ($width - $deltaXInicial) / $parameters['rangeMin'];
            foreach($graficos as &$grafico){
                foreach($grafico['curvas'] as &$curva){  //OJO va por referencia
//                    $sep = ' M ';
                    $prevIsNull = true;
                    $minAnt = 0;
                    foreach($minutos as $minuto){ 
                        if (isset($minuto->{$curva['campo']})){
                            $valor = $minuto->{$curva['campo']};
                            if ($prevIsNull){
                                $sep = ' M ';
                            }else{
                                if ($minuto->MinAbsUTC - $minAnt > $parameters['deltaMin']){
                                    $sep = ' M ';
                                }else{
                                    $sep = ' L ';
                                }
                            };
                            $prevIsNull = false;
                        }else{
                            $valor = 35;
                            $prevIsNull = true;
                            $sep = ' M ';
                        }
                        $minAnt = $minuto->MinAbsUTC;

                        $curva['Path'] .= $sep . (($minuto->MinAbsUTC - $parameters['startMin']) * $deltaX + $deltaXInicial) . ' ' . (400 - $valor * 10);      // Funciona con DB que devuelve un array de stdClass
//                        $sep = ' L '; 
                    }                      
                }
                unset($curva);                               //Necesario al ir por referencia
            }

            unset($grafico);
        @endphp
            
    <div id="line_top_x">
        @foreach($graficos as $grafico)
        <div style="position: relative; width: 1800px; height: 800px;">
            <div style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%;">
                <svg width="{{$width}}" height="800">
                    <g>
                    @foreach($grafico['curvas'] as &$curva)
                        <path d="{{$curva['Path']}}" fill="none" stroke="{{$curva['Color']}}" stroke-width="2" clip-path="url(#rablfilter0)">
                        </path>
                    @endforeach
                    </g>
                </svg>
            </div>
        </div>
        @endforeach
    </div>
    </body>
</html>

<html>
