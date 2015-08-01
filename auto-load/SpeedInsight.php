<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
class SpeedInsight {

    public $desktopResult, $mobileResult, $url;

    function __construct($url) {
        $this->url = $url;
        $this->desktopResult = HttpRequest::queryJSON(array(
                    'url' => 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed',
                    'data' => array(
                        'url' => $url,
                        'strategy' => 'desktop',
//                        'rule' => 'AvoidLandingPageRedirects'
                    ),
        ));
        $this->mobileResult = HttpRequest::queryJSON(array(
                    'url' => 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed',
                    'data' => array(
                        'url' => $url,
                        'strategy' => 'mobile',
//                        'rule' => 'AvoidLandingPageRedirects'
                    ),
        ));
//        var_dump($this->desktopResult);
//        die();
    }

    public function getScoreClassName($score) {
        if ($score >= 80)
            return 'green';
        if ($score >= 50)
            return 'orange';
        return 'red';
    }

    public function returnJson() {
        $desktopScore = $this->desktopResult->ruleGroups->SPEED->score;
        $mobileScore = $this->mobileResult->ruleGroups->SPEED->score;
        $mobileUsability = $this->mobileResult->ruleGroups->USABILITY->score;
//        die($mobileUsability);
        $result = array(
            'desktop-score' => $desktopScore,
            'desktop-class' => $this->getScoreClassName($desktopScore),
            'mobile-score' => $mobileScore,
            'mobille-class' => $this->getScoreClassName($mobileScore),
            'usability-score' => $mobileUsability,
            'usability-class' => $this->getScoreClassName($mobileUsability),
        );
        return json_encode($result);
    }

    public function getUrl($strategy = 'desktop') {
        return "https://developers.google.com/speed/pagespeed/insights/?url=$this->url&tab=$strategy";
    }

    public static function getCurrentUrl($strategy = 'desktop') {
        global $speedInsightPlugin;
        if (is_admin())
            $url = home_url();
        else
            $url = $speedInsightPlugin->getCurrentURL();
        return "https://developers.google.com/speed/pagespeed/insights/?url=$url&tab=$strategy";
    }

}
