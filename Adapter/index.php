<?php
//Суть в том, что мы оборачиваем объект специальным адаптером, который содержит ссылку на объект.
//Таким образом, адаптер содержит в себе объект и переврдит тарабаржину объекта на язык, понятный другим частям программы.
//Например, если вся программа работает с JSON, а объект работает с XML.
//Адаптер будет переводить XML в JSON, перед тем, как передать данные в основную программу.


//ИГРУШЕЧНЫЙ ПРИМЕР



class RoundHole{

    private int $_radius = 0;

    public function __construct(int $radius){
        $this->_radius = $radius;
    }

    private function getRadius(): int{
        return $this->_radius;
    }

    public function fits($peg): bool{
        return $this->getRadius() >= $peg->getRadius();
    }
}


class RoundPeg{

    private int $_radius = 0;

    public function __construct(int $radius){
        $this->_radius = $radius;
    }

    public function getRadius(): int{
        return $this->_radius;
    }
}


class SquarePeg{

    private int $_width = 0;

    public function __construct(int $width){
        $this->_width = $width;
    }

    public function getWidth(): int{
        return $this->_width;
    }
}

//Засовываем круглую форму в круглую дырку

$hole = new RoundHole(113);  //Круглое отверстие, с радиусом 113
$roundPeg = new RoundPeg(3); //Круглая форма(цилиндр) с радиусом 3
$isFit = $hole->fits($roundPeg); //Если радиус отверстия больше или равен радиусу цилиндра то true иначе false
var_dump($isFit);


//Но что если мы захотим засунуть в круглую дырку, квадратную форму?
//У квадратной формы нет свойства "радиус", а засунуть хочется
//Поэтому пишем адаптер, который представит квадратную форму, как круглую и засунет в дырку


class SquarePegAdapter extends RoundPeg{
    private SquarePeg $_peg;

    public function __construct(SquarePeg $squarePeg){
        $this->_peg = $squarePeg;
    }

    public function getRadius(): int{
        return $this->_peg->getWidth() * sqrt(2) / 2;
    }
}

$hole = new RoundHole(113);  //Круглое отверстие, с радиусом 113
$squarePeg = new SquarePeg(114); //Круглая форма(цилиндр) с радиусом 3
$isFit = $hole->fits(new SquarePegAdapter($squarePeg)); //Если радиус отверстия больше или равен радиусу цилиндра то true иначе false
var_dump($isFit);
