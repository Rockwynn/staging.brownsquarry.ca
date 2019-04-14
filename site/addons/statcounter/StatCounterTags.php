<?php

namespace Statamic\Addons\statcounter;

use Statamic\Extend\Tags;

class StatCounterTags extends Tags
{

  public function __call($method, $params) {
      return $this->index($method);
  }

  public function index() {

    $project = $this->getConfig('project_id', 'default');
    $security = $this->getConfig('security_code', 'default');

    return '
      <script type="text/javascript">
      var sc_project=' . $project . ';
      var sc_invisible=1;
      var sc_security="' . $security . '";
      </script>
      <script type="text/javascript"
      src="https://www.statcounter.com/counter/counter.js"
      async></script>
      <noscript><div class="statcounter"><a title="Web Analytics
      Made Easy - StatCounter" href="http://statcounter.com/"
      target="_blank"><img class="statcounter"
      src="//c.statcounter.com/' . $project . '/0/' . $security . '/1/" alt="Web
      Analytics Made Easy - StatCounter"></a></div></noscript>
    ';

  }

}
