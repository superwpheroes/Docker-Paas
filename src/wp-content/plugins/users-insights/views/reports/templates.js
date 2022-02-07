angular.module('usinReportsApp').run(['$templateCache', function($templateCache) {
  'use strict';

  $templateCache.put('views/reports/chart.html',
    "<div>\n" +
    "	<canvas width=\"400\" height=\"400\" class=\"usin-chart-box\"></canvas>\n" +
    "</div>"
  );


  $templateCache.put('views/reports/main.html',
    "<div class=\"usin-tabs\">\n" +
    "	<ul>\n" +
    "	<li ng-class=\"['usin-tab', {'usin-tab-selected': $ctrl.currentGroup == group}]\" \n" +
    "		ng-repeat=\"group in $ctrl.reportGroups\" ng-click=\"$ctrl.changeGroup(group)\">\n" +
    "		<span class=\"usin-tab-text\">{{group.name}}</span>\n" +
    "	</li>\n" +
    "	<usin-report-toggle reports=\"$ctrl.reports\" group=\"$ctrl.currentGroup\" \n" +
    "		on-visibility-change=\"$ctrl.changeReportVisibility(report, newVisibility)\"></usin-report-toggle>\n" +
    "	</ul>\n" +
    "	\n" +
    "</div>\n" +
    "<div class=\"usin-reports\">\n" +
    "	<usin-report ng-repeat=\"ro in $ctrl.reports | group: $ctrl.currentGroup | filter:{visible:true}\" \n" +
    "		report-options=\"ro\" class=\"usin-report-box\">\n" +
    "	</usin-report>\n" +
    "</div>\n" +
    "<div class=\"clear\"></div>\n" +
    "<div ng-if=\"$ctrl.currentGroup.info\" class=\"usin-group-info\">\n" +
    "	<p>{{$ctrl.currentGroup.info}}</p>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"usin-no-reports-found notice notice-warning\" ng-if=\"!($ctrl.reports | group:$ctrl.currentGroup).length\">\n" +
    "	<p>{{$ctrl.strings.noReportsFound}}</p>\n" +
    "</div>\n"
  );


  $templateCache.put('views/reports/report-toggle.html',
    "<div class=\"usin-report-toggle\">\n" +
    "	<md-tooltip md-direction=\"top\">{{$ctrl.strings.toggleReports}}</md-tooltip>\n" +
    "	<span ng-click=\"$ctrl.toggleMenu()\"> \n" +
    "		<span class=\"usin-reports-visible\">{{($ctrl.reports | group:$ctrl.group | filter:{visible:true}).length}}/{{($ctrl.reports | group:$ctrl.group).length}}</span>\n" +
    "		<span class=\"usin-icon-visible usin-btn-drop-down usin-reports-icon\" ng-class=\"{'usin-btn-drop-down-opened' : $ctrl.displayed === true}\"/>\n" +
    "	</span>\n" +
    "	<div class=\"usin-fields-settings usin-drop-down\" ng-show=\"$ctrl.displayed\">\n" +
    "		<ul>\n" +
    "			<li ng-repeat=\"report in $ctrl.reports | group:$ctrl.group\">\n" +
    "				<span>\n" +
    "					<md-checkbox ng-checked=\"report.visible\" ng-click=\"$ctrl.onCheckboxChange(report)\" md-no-ink=\"true\"\n" +
    "						aria-label=\"Toggle report {{report.name}}\"></md-checkbox>\n" +
    "					{{report.name}}\n" +
    "				</span>\n" +
    "			</li>\n" +
    "		</ul>\n" +
    "	</div>\n" +
    "</div>"
  );


  $templateCache.put('views/reports/report.html',
    "<div ng-class=\"['usin-report-wrap', {'usin-simple-loading': $ctrl.loading}]\">\n" +
    "	<div class=\"usin-report-header\">\n" +
    "		<span class=\"usin-report-title\">{{$ctrl.reportOptions.name}}</span>\n" +
    "		<span class=\"usin-icon-info\" ng-if=\"$ctrl.reportOptions.info\">\n" +
    "			<md-tooltip md-direction=\"right\">{{$ctrl.reportOptions.info}}</md-tooltip>\n" +
    "		</span>\n" +
    "		\n" +
    "		<ui-select ng-model=\"$ctrl.filter\" ng-change=\"$ctrl.onFilterChange()\" ng-if=\"$ctrl.hasFilters()\" \n" +
    "			theme=\"select2\" search-enabled=\"{{$ctrl.shouldEnableSearch()}}\" ng-disabled=\"$ctrl.loading\">\n" +
    "			<ui-select-match>{{$ctrl.reportOptions.filters.options[$ctrl.filter]}}</ui-select-match>\n" +
    "			<ui-select-choices repeat=\"item.key as (key , item) in $ctrl.reportOptions.filters.options | filter: $select.search\" position=\"down\">\n" +
    "				<span>{{item.value}}</span>\n" +
    "			</ui-select-choices>\n" +
    "		</ui-select>\n" +
    "\n" +
    "		<div class=\"clear\"></div>\n" +
    "	</div>\n" +
    "	<div class=\"usin-report-graph\">\n" +
    "		<div ng-if=\"!$ctrl.loading && !$ctrl.error\">\n" +
    "			<usin-chart chart-options=\"$ctrl.chartOptions\"></usin-chart>\n" +
    "		</div>\n" +
    "		<div ng-if=\"$ctrl.error\" class=\"usin-error\">\n" +
    "			{{$ctrl.error}}\n" +
    "		</div>\n" +
    "	</div>\n" +
    "</div>"
  );

}]);
