<?php
    class pagination{
        private $handler;

        public function __construct($handler){
            $this->handler  = $handler;
        }

        public function getPagination($pagenumber, $pages){
            global $website_url;

            $str = null;
            $x = 0;
            
            foreach($_GET as $key => $value){
                $getPage[$x] = $key;
                $getPageValue[$x] = $value;
                $x++;
            }

            $str .= '<div class="row"><div class="col-md-12"><nav aria-label="Page navigation" class="float-end"><ul class="pagination">';

                $i = 1;
                $str .= '<li><a href="' . $website_url . '/' . $getPageValue[0] . '?' . $getPage[1] . '=' . $getPageValue[1] . '&pn=1" class="page-link">&lt;&lt;</a></li>';
                $str .= '<li><a href="' . $website_url . '/' . $getPageValue[0] . '?' . $getPage[1] . '=' . $getPageValue[1] . '&pn=' . ($pagenumber - 1) . '" class="page-link">&lt;</a></li>';
    
                while($i <= $pages){
                    if(!($pagenumber <= $i-5) && !($pagenumber >= $i+5)){
                        $str .= '<li ' . (($pagenumber === $i)? 'class="active"' : '') . '><a href="' . $website_url . '/' . $getPageValue[0] . '?' . $getPage[1] . '=' . $getPageValue[1] . '&pn=' . $i . '" class="page-link">' . $i . '</a></li>';
                    }
                    $i++;
                }
                $pagenumber += 1;
                
                $str .= '<li class="page-item"><a href="' . $website_url . '/' . $getPageValue[0] . '?' . $getPage[1] . '=' . $getPageValue[1] . '&pn=' . $pagenumber . '" class="page-link">&gt;</a></li>';
                $str .= '<li class="page-item"><a href="' . $website_url . '/' . $getPageValue[0] . '?' . $getPage[1] . '=' . $getPageValue[1] . '&pn=' . $pages . '" class="page-link">&gt;&gt;</a></li>';
                
            $str .= '</ul></nav></div></div>';

            return $str;
        }
    }
?>