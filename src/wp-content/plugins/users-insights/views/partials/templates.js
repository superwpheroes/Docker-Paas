angular.module('usinPartials').run(['$templateCache', function($templateCache) {
  'use strict';

  $templateCache.put('views/partials/checkboxes-field.html',
    "<div>\n" +
    "\n" +
    "	<ul class=\"usin-checkbox-options\">\n" +
    "		<li ng-repeat=\"(key, val) in $ctrl.options\">\n" +
    "			<md-checkbox ng-checked=\"$ctrl.isOptionChecked(key)\" ng-click=\"$ctrl.onOptionClick(key)\" md-no-ink=\"true\"\n" +
    "				aria-label=\"{{val}}\"></md-checkbox>\n" +
    "			{{val}}\n" +
    "		</li>\n" +
    "	</ul>\n" +
    "\n" +
    "\n" +
    "</div>"
  );


  $templateCache.put('views/partials/date-field.html',
    "	<md-datepicker md-open-on-focus ng-model=\"$ctrl.date\" ng-change=\"$ctrl.doOnChange()\"></md-datepicker>"
  );


  $templateCache.put('views/partials/info-icon.html',
    "<span class=\"usin-icon-info\">\n" +
    "	<md-tooltip class=\"usin-multiline-tooltip\">{{$ctrl.text}}</md-tooltip>\n" +
    "</span>"
  );


  $templateCache.put('views/partials/select-field.html',
    "<span>\n" +
    "		<ui-select usin-select-focus-input search-enabled=\"searchEnabled\" theme=\"select2\" ng-class=\"{'usin-select-loading':loading}\">\n" +
    "			<ui-select-match placeholder=\"{{placeholder}}\">{{$select.selected[optionVal]}}</ui-select-match>\n" +
    "			<ui-select-choices ui-disable-choice=\"shouldDisableChoice(field)\" refresh=\"searchOptions($select)\" refresh-delay=\"300\" repeat=\"field[optionKey] as field in options | filter: $select.search\" position=\"down\">\n" +
    "				<span ng-if=\"field.icon\" class=\"usin-icon-{{field.icon}}\"></span>\n" +
    "				  <span ng-bind-html=\"field[optionVal] | highlight: $select.search\"></span>\n" +
    "			</ui-select-choices>\n" +
    "		</ui-select>\n" +
    "	</span>"
  );

}]);
