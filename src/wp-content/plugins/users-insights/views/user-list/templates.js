angular.module('usinApp').run(['$templateCache', function($templateCache) {
  'use strict';

  $templateCache.put('views/user-list/add-separator.html',
    "<div class=\"usin-add-separator-wrap\">\n" +
    "	<label>{{strings.addSeparator}}</label>\n" +
    "	<input type=\"text\" ng-model=\"title\" class=\"usin-input\" ng-keyup=\"$event.keyCode==13 && add()\">\n" +
    "	<button class=\"usin-btn usin-btn-main usin-btn-small\" ng-click=\"add()\">{{strings.add}}</button>\n" +
    "</div>"
  );


  $templateCache.put('views/user-list/bulk-actions.html',
    "<div class=\"usin-bulk-actions-wrap\" ng-show=\"bulkActions.getCount()>0\">\n" +
    "	<div class=\"usin-checked-users usin-float-left\">\n" +
    "		<p>{{ bulkActions.getCount() === 1 ? strings.userSelected : strings.usersSelected.replace('%d', bulkActions.getCount())}}</p>\n" +
    "		\n" +
    "	</div>\n" +
    "	\n" +
    "	<div class=\"usin-btn\" ng-click=\"toggleOptions()\" ng-class=\"{'usin-btn-drop-down-opened' : optionsVisible === true}\">\n" +
    "		{{strings.bulkActions}}\n" +
    "		<span class=\"usin-icon-drop-down usin-btn-drop-down\"></span>\n" +
    "	</div>\n" +
    "	<div class=\"usin-drop-down usin-bulk-actions-options usin-animate\" ng-show=\"optionsVisible\" click-outside=\"optionsVisible=false\">\n" +
    "		<ul>\n" +
    "		    <li ng-click=\"showGroupEditDialog('add')\">{{strings.addGroup}}</li>\n" +
    "		    <li ng-click=\"showGroupEditDialog('remove')\">{{strings.removeGroup}}</li>\n" +
    "		</ul>\n" +
    "  	</div>\n" +
    "	\n" +
    "   \n" +
    "</div>"
  );


  $templateCache.put('views/user-list/confirm-dialog.html',
    "<md-dialog aria-label=\"{{title}}\">\n" +
    "	<md-toolbar>\n" +
    "		<div class=\"md-toolbar-tools\">\n" +
    "			<h2>{{title}}</h2>\n" +
    "			<span flex></span>\n" +
    "			<md-button class=\"md-icon-button\" ng-click=\"close()\">\n" +
    "				<md-icon class=\"usin-icon-delete\" aria-label=\"Close dialog\"></md-icon>\n" +
    "			</md-button>\n" +
    "		</div>\n" +
    "	</md-toolbar>\n" +
    "\n" +
    "	<md-dialog-content>\n" +
    "		<div class=\"md-dialog-content\">\n" +
    "			<div ng-if=\"content\" ng-bind-html=\"content\"></div>\n" +
    "			<div ng-if=\"message\">\n" +
    "				<p>{{message}}</p>\n" +
    "			</div>\n" +
    "			<div class=\"usin-error\" ng-if=\"error\">{{error}}</div>\n" +
    "		</div>\n" +
    "	</md-dialog-content>\n" +
    "\n" +
    "	<md-dialog-actions layout=\"row\">\n" +
    "		<div class=\"usin-icon-simple-loading\" ng-show=\"isLoading\"></div>\n" +
    "		<button class=\"usin-btn\" ng-click=\"close()\">\n" +
    "			{{strings.cancel}}\n" +
    "		</button>\n" +
    "		<button class=\"usin-btn usin-btn-main\" ng-click=\"callAction()\">\n" +
    "			{{actionText}}\n" +
    "		</button>\n" +
    "	</md-dialog-actions>\n" +
    "</md-dialog>"
  );


  $templateCache.put('views/user-list/create-segment-dialog.html',
    "<md-dialog aria-label=\"{{strings.newSegment}}\">\n" +
    "	<form ng-cloak>\n" +
    "		<md-toolbar>\n" +
    "			<div class=\"md-toolbar-tools\">\n" +
    "				<h2>{{strings.newSegment}}</h2>\n" +
    "				<span flex></span>\n" +
    "				<md-button class=\"md-icon-button\" ng-click=\"closeDialog()\">\n" +
    "					<md-icon class=\"usin-icon-delete\" aria-label=\"Close dialog\"></md-icon>\n" +
    "				</md-button>\n" +
    "			</div>\n" +
    "		</md-toolbar>\n" +
    "\n" +
    "		<md-dialog-content>\n" +
    "			<div class=\"md-dialog-content\">\n" +
    "				<label>{{strings.segmentName}}</label>\n" +
    "				<input ng-model=\"segmentName\" class=\"usin-input\" type=\"text\" ng-keypress=\"$event.keyCode==13 && doOnEnter($event)\">\n" +
    "				<div class=\"usin-error\" ng-if=\"segmentError\">{{segmentError}}</div>\n" +
    "			</div>\n" +
    "		</md-dialog-content>\n" +
    "\n" +
    "		<md-dialog-actions layout=\"row\">\n" +
    "			<div class=\"usin-icon-simple-loading\" ng-show=\"isLoading\"></div>\n" +
    "			<button class=\"usin-btn\" ng-click=\"closeDialog()\">\n" +
    "				{{strings.cancel}}\n" +
    "			</button>\n" +
    "			<button class=\"usin-btn usin-btn-main\" ng-click=\"saveSegment()\" ng-disabled=\"!segmentName\">\n" +
    "				{{strings.saveSegment}}\n" +
    "			</button>\n" +
    "		</md-dialog-actions>\n" +
    "	</form>\n" +
    "</md-dialog>"
  );


  $templateCache.put('views/user-list/date-pick.html',
    "<span>\n" +
    "	<span ng-show=\"isDateOperator(operator)\">\n" +
    "		<md-datepicker ng-model=\"date\" ng-change=\"updateDate()\" md-open-on-focus></md-datepicker>\n" +
    "	</span>\n" +
    "\n" +
    "	<span ng-show=\"!isDateOperator(operator)\" class=\"usin-days-ago-filter\">\n" +
    "		<input type=\"number\" min=\"0\" ng-model=\"daysAgo\" ng-change=\"updateDaysAgo()\" class=\"usin-number-field\" />\n" +
    "		<span class=\"usin-filter-suffix\">{{strings.daysAgo}}</span>\n" +
    "	</span>\n" +
    "</span>"
  );


  $templateCache.put('views/user-list/filter-combined.html',
    "<span>\n" +
    "	<span class=\"usin-filter-operator\">{{strings.with}}</span>\n" +
    "	<span ng-repeat=\"(i, item) in condition\">\n" +
    "		\n" +
    "		<span class=\"usin-combined-filter-item\">\n" +
    "			<usin-select-field ng-model=\"item.id\" options=\"items\" option-key=\"id\" \n" +
    "				option-val=\"name\" ng-change=\"doOnFieldSelected(i, item.id)\" disabled-choices=\"disabledItems\" class=\"usin-select-small\"></usin-select-field>\n" +
    "			\n" +
    "				<span ng-if=\"item.type == 'number'\">\n" +
    "					<!-- filter by number -->\n" +
    "					<span class=\"usin-combined-filter-word\">{{strings.between}}</span><input type=\"number\" ng-model=\"item.val[0]\">\n" +
    "					<span class=\"usin-combined-filter-word\">{{strings.and}}</span><input type=\"number\" ng-model=\"item.val[1]\">\n" +
    "				</span>\n" +
    "			\n" +
    "				<span ng-if=\"item.type == 'select'\">\n" +
    "					<!-- filter by select -->\n" +
    "					<usin-select-field ng-model=\"item.val\" options=\"item.options\" class=\"usin-select-medium\" \n" +
    "						search-action=\"item.searchAction\"></usin-select-field>\n" +
    "				</span>\n" +
    "			\n" +
    "				<span ng-if=\"item.type == 'date'\">\n" +
    "					<!-- filter by date -->\n" +
    "					<span class=\"usin-combined-filter-word\">{{strings.between}}</span><usin-date-field ng-model=\"item.val[0]\"></usin-date-field>\n" +
    "					<span class=\"usin-combined-filter-word\">{{strings.and}}</span><usin-date-field ng-model=\"item.val[1]\"></usin-date-field>\n" +
    "				</span>\n" +
    "			\n" +
    "		</span>\n" +
    "\n" +
    "		<span ng-if=\"i<condition.length-1\" class=\"usin-filter-operator\">{{strings.and}}</span>\n" +
    "\n" +
    "	</span>\n" +
    "	<button class=\"usin-btn usin-btn-apply usin-btn-add usin-icon-add\" ng-click=\"addItem()\" ng-disabled=\"!canAddMore()\">\n" +
    "		<md-tooltip md-direction=\"top\">{{strings.and}}</md-tooltip>\n" +
    "	</button>\n" +
    "</span>"
  );


  $templateCache.put('views/user-list/filter.html',
    "<div class=\"usin-filter-wrap\">\n" +
    "		<button class=\"usin-btn usin-btn-main usin-btn-filter\" ng-click=\"addFilter()\" ng-class=\"{'usin-btn-disabled': loading.isLoading()}\">\n" +
    "			<span class=\"usin-icon-filter usin-icon-left\" /> {{strings.addFilter}}\n" +
    "		</button>\n" +
    "		<div class=\"usin-filter-set\" ng-repeat=\"(key, filter) in filters\">\n" +
    "	\n" +
    "			<div ng-if=\"filter.editing\" class=\"usin-filter-form\" ng-class=\"{'usin-filter-form-combined': filter.type == 'combined'}\">\n" +
    "				<usin-select-field ng-model=\"filter.by\" options=\"fields\" option-key=\"id\" option-val=\"name\" \n" +
    "					ng-change=\"doOnFieldSelected(key, filter)\" class=\"usin-field-select usin-filter-by-select\"></usin-select-field>\n" +
    "				<span ng-if=\"filter.type\" ng-keyup=\"$event.keyCode==13 && applyFilter(filter)\">\n" +
    "	\n" +
    "					<span ng-if=\"filter.type!='combined'\">\n" +
    "						<usin-select-field ng-model=\"filter.operator\" options=\"filter.operators\" ng-hide=\"filter.operators.length<=1\" class=\"usin-operator-select\"></usin-select-field>\n" +
    "						<span ng-if=\"filter.isOptionType()\">\n" +
    "							<!-- filter a select field -->\n" +
    "							<usin-select-field ng-model=\"filter.condition\" options=\"filter.options\" search-action=\"filter.searchAction\"\n" +
    "								ng-hide=\"filter.isNullOperator()\" class=\"usin-condition-select\"></usin-select-field>\n" +
    "						</span>\n" +
    "						\n" +
    "						<span ng-if=\"filter.isDateType()\">\n" +
    "							<!-- filter by date -->\n" +
    "							<span usin-date-filter condition=\"filter.condition\" operator=\"filter.operator\" by=\"filter.by\"\n" +
    "								ng-hide=\"filter.isNullOperator()\"></span>\n" +
    "						</span>\n" +
    "	\n" +
    "						<span ng-if=\"filter.type=='number'\">\n" +
    "							<!-- filter by number -->\n" +
    "							<input type=\"number\" ng-model=\"filter.condition\" ng-hide=\"filter.isNullOperator()\">\n" +
    "						</span>\n" +
    "	\n" +
    "						<span ng-if=\"filter.isTextField()\">\n" +
    "							<!-- filter by text -->\n" +
    "							<input type=\"text\" ng-model=\"filter.condition\" ng-hide=\"filter.isNullOperator()\">\n" +
    "						</span>\n" +
    "					</span>\n" +
    "					<usin-filter-combined ng-if=\"filter.type=='combined'\" items=\"filter.field.filter.items\" condition=\"filter.condition\" class=\"usin-combined-filter-items\"></usin-filter-combined>\n" +
    "				</span>\n" +
    "				<span class=\"usin-filter-actions\">\n" +
    "					<button ng-show=\"filter.type\" class=\"usin-btn usin-btn-main usin-btn-apply usin-icon-apply\" ng-click=\"applyFilter(filter)\" ></button>\n" +
    "					<span class=\"usin-btn-close usin-icon-close\" ng-click=\"remove(filter)\" />\n" +
    "				</span>\n" +
    "			</div>\n" +
    "			\n" +
    "			<div ng-if=\"!filter.editing\" class=\"usin-filter-preview usin-btn\" ng-class=\"{'usin-disabled': filter.disabled, 'usin-filter-preview-combined': filter.type == 'combined'}\">\n" +
    "				<md-tooltip md-direction=\"top\" ng-if=\"filter.disabled\">{{strings.fieldNotExist}}</md-tooltip>\n" +
    "				<span class=\"usin-filter-preview-text\" ng-click=\"edit(filter)\">\n" +
    "					<span class=\"usin-filter-preview-label\">{{filter.label}}</span> <span class=\"usin-filter-operator\">{{filter.previewOperator()}}</span> <span ng-class=\"{'usin-filter-preview-combined-items':filter.type=='combined'}\" ng-bind-html=\"filter.previewCondition()\"></span>\n" +
    "				</span>\n" +
    "				<span class=\"usin-btn-close usin-icon-close\" ng-click=\"remove(filter)\" ng-class=\"{'usin-btn-disabled': loading.isLoading()}\" />\n" +
    "			</div>\n" +
    "		</div>\n" +
    "	\n" +
    "		<span ng-show=\"filters.length>1 && !filtersPending()\" class=\"usin-btn-text\" ng-click=\"clearAll()\">{{strings.clearAll}}</span>\n" +
    "	\n" +
    "	</div>"
  );


  $templateCache.put('views/user-list/group-edit-dialog.html',
    "<md-dialog aria-label=\"{{title}}\">\n" +
    "	<form ng-cloak>\n" +
    "		<md-toolbar>\n" +
    "			<div class=\"md-toolbar-tools\">\n" +
    "				<h2>{{title}}</h2>\n" +
    "				<span flex></span>\n" +
    "				<md-button class=\"md-icon-button\" ng-click=\"cancel()\">\n" +
    "					<md-icon class=\"usin-icon-delete\" aria-label=\"Close dialog\"></md-icon>\n" +
    "				</md-button>\n" +
    "			</div>\n" +
    "		</md-toolbar>\n" +
    "\n" +
    "		<md-dialog-content class=\"usin-group-edit-wrap\">\n" +
    "			<div class=\"md-dialog-content\">\n" +
    "				<div ng-show=\"groups.length\">\n" +
    "					<p>{{info}}</p>\n" +
    "\n" +
    "					<md-input-container>\n" +
    "						<md-select ng-model=\"selectedGroup\" placeholder=\"{{strings.selectGroup}}\">\n" +
    "							<md-option ng-value=\"group.key\" ng-repeat=\"group in groups\" md-no-ink=\"true\">{{ group.val }}</md-option>\n" +
    "						</md-select>\n" +
    "					</md-input-container>\n" +
    "\n" +
    "					<div class=\"usin-error\" ng-if=\"error\">{{error}}</div>\n" +
    "				</div>\n" +
    "				<div ng-show=\"!groups.length\">\n" +
    "					<p>{{strings.noGroups}}</p>\n" +
    "				</div>\n" +
    "			</div>\n" +
    "		</md-dialog-content>\n" +
    "\n" +
    "		<md-dialog-actions layout=\"row\">\n" +
    "			<div class=\"usin-icon-simple-loading\" ng-show=\"isLoading\"></div>\n" +
    "			<span flex></span>\n" +
    "			<button class=\"usin-btn\" ng-click=\"cancel()\">\n" +
    "				{{strings.cancel}}\n" +
    "			</button>\n" +
    "			<button class=\"usin-btn usin-btn-main\" ng-click=\"apply()\" ng-disabled=\"!selectedGroup\" ng-show=\"groups.length\">\n" +
    "				{{strings.apply}}\n" +
    "			</button>\n" +
    "\n" +
    "		</md-dialog-actions>\n" +
    "	</form>\n" +
    "</md-dialog>"
  );


  $templateCache.put('views/user-list/list-options.html',
    "<div class=\"usin-options-wrap\">\n" +
    "	<div class=\"usin-bulk-actions usin-float-left\" ng-if=\"listView && canUpdateUsers\"></div>\n" +
    "	<div class=\"usin-segments\"></div>\n" +
    "	\n" +
    "	<button class=\"usin-btn usin-btn-export\" ng-if=\"canExportUsers\"\n" +
    "		ng-click=\"showConfirm()\" ng-disabled=\"!listView || bulkActions.isAnyChecked() || loading.isLoading() || !total.current\"> \n" +
    "		<span class=\"usin-icon-export\" />\n" +
    "		<md-tooltip md-direction=\"top\">{{strings.export.replace('%d', total.current)}}</md-tooltip>\n" +
    "	</button>\n" +
    "\n" +
    "	<button class=\"usin-btn usin-btn-list-options\" ng-click=\"toggleDisplayed()\"\n" +
    "		ng-disabled=\"!listView || bulkActions.isAnyChecked() || loading.isLoading()\"> \n" +
    "		<span class=\"usin-icon-visible usin-btn-drop-down\" ng-class=\"{'usin-btn-drop-down-opened' : displayed === true}\"/>\n" +
    "		<md-tooltip md-direction=\"top\">{{strings.toggleColumns}}</md-tooltip>\n" +
    "	</button>\n" +
    "		\n" +
    "	<button class=\"usin-btn usin-btn-map\" ng-click=\"onToggleView()\" ng-disabled=\"bulkActions.isAnyChecked() || \n" +
    "		loading.isLoading()  || (listView && !total.current)\"\n" +
    "		ng-class=\"{'usin-btn-map-active' : !listView}\" ng-if=\"showMap\"> \n" +
    "		<span class=\"usin-icon-map\"/>\n" +
    "		<md-tooltip md-direction=\"top\" md-autohide>{{listView ? strings.enterMapView : strings.exitMapView}}</md-tooltip>\n" +
    "	</button>\n" +
    "	<div class=\"usin-fields-settings usin-drop-down usin-animate ng-hide\" ng-show=\"displayed\" click-outside=\"displayed=false\">\n" +
    "		<ul dnd-list=\"fields\">\n" +
    "			<li ng-repeat=\"field in fields\" dnd-draggable=\"field\" dnd-moved=\"reorder($index)\" dnd-disable-if=\"field.disableHide\">\n" +
    "				<dnd-nodrag>\n" +
    "					<span>\n" +
    "						<md-checkbox ng-checked=\"field.show\" ng-click=\"onCheckboxChange(field)\" md-no-ink=\"true\"\n" +
    "							aria-label=\"Toggle Column {{field.name}}\" ng-disabled=\"loading.isLoading() || field.disableHide\"></md-checkbox>\n" +
    "						<span class=\"usin-icon-{{field.icon}}\"></span>\n" +
    "						{{field.name}}\n" +
    "						<div dnd-handle class=\"usin-drag-handle usin-icon-sort\" ng-if=\"!field.disableHide\"></div>\n" +
    "						<div class=\"usin-drag-handle usin-disabled usin-icon-sort\" ng-if=\"field.disableHide\"></div>\n" +
    "					</span>\n" +
    "				</dnd-nodrag>\n" +
    "			</li>\n" +
    "			<li class=\"dndPlaceholder\"><label></label></li>\n" +
    "\n" +
    "		</ul>\n" +
    "	</div>\n" +
    "</div>\n" +
    "\n" +
    "\n"
  );


  $templateCache.put('views/user-list/list.html',
    "<div>\n" +
    "	<div class=\"usin-table-wrap\">\n" +
    "	<table class=\"usin-table usin-user-table\" ng-class=\"{'usin-bulk-actions-checked': bulkActions.isAnyChecked(), 'usin-table-no-rows': !userList.users.length}\">\n" +
    "	<thead>\n" +
    "		<tr>\n" +
    "			<th ng-repeat=\"field in showFields\" ng-class=\"{'usin-sortable' : field.order !== false}\">\n" +
    "				<span ng-if=\"field.id=='username' && userList.users.length\" class=\"usin-heading-checkbox\">\n" +
    "					<md-checkbox aria-label=\"Select All\"\n" +
    "								ng-checked=\"bulkActions.isAllChecked()\"\n" +
    "								md-indeterminate=\"bulkActions.isAllIndeterminate()\"\n" +
    "								ng-click=\"bulkActions.toggleAll()\"\n" +
    "								class=\"usin-toggler-checkbox\">\n" +
    "					</md-checkbox>\n" +
    "					<md-tooltip md-direction=\"top\">{{bulkActions.isAllChecked() ? strings.clearSelection: strings.selectAllUsers}}</md-tooltip>\n" +
    "				</span>\n" +
    "				<span ng-click=\"setOrderBy(field.id)\">\n" +
    "					<span class=\"usin-heading-{{field.id}}\">{{field.name}}</span>\n" +
    "					<span class=\"usin-order-arrow\" ng-class=\"{'usin-order-arrow-up' : userList.order == 'ASC', 'usin-order-arrow-down': userList.order == 'DESC'}\"\n" +
    "						ng-show=\"userList.orderBy==field.id\"></span>\n" +
    "				</span>\n" +
    "			</th>\n" +
    "		</tr>\n" +
    "	</thead>\n" +
    "	<tr ng-repeat=\"user in userList.users\">\n" +
    "		<td ng-repeat=\"field in showFields\" ng-switch=\"field.id\" title=\"{{field.name}} ({{user.username}})\" class=\"usin-field-{{field.id}}\">\n" +
    "			<span ng-switch-when=\"username\" class=\"usin-username-clickable usin-username-wrap\">\n" +
    "				<span class=\"usin-online-circle\" ng-if=\"user.online\" title=\"{{strings.online}}\"></span>\n" +
    "				<span class=\"user-avatar-actions\">\n" +
    "					<span ng-bind-html=\"user.avatar\" class=\"usin-avatar-wrap\"></span>\n" +
    "					<md-checkbox ng-checked=\"bulkActions.isChecked(user.ID)\" ng-click=\"bulkActions.toggle(user.ID)\" \n" +
    "						aria-label=\"Select User\" md-no-ink=\"true\"></md-checkbox>\n" +
    "				</span>\n" +
    "				<a class=\"usin-username\" ng-href=\"#/user/{{user.ID}}\">{{user.username}}</a>\n" +
    "			</span>\n" +
    "			\n" +
    "			<span ng-switch-when=\"user_groups\">\n" +
    "				<span ng-repeat=\"groupId in user.user_groups\" ng-bind-html=\"groupId | groupTagHtml\"></span>\n" +
    "			</span>\n" +
    "\n" +
    "			<span ng-switch-default>\n" +
    "				<span ng-if=\"field.allowHtml\" ng-bind-html=\"user[field.id]\" class=\"usin-field-value\"></span>\n" +
    "				<span ng-if=\"!field.allowHtml\" class=\"usin-field-value\">{{user[field.id]}}</span>\n" +
    "			</span>\n" +
    "		</td>\n" +
    "	</tr>\n" +
    "	<tfoot ng-show=\"userList.users.length\" >\n" +
    "		<tr>\n" +
    "			<th ng-repeat=\"field in showFields\" ng-class=\"{'usin-sortable' : field.order !== false}\">\n" +
    "				<span ng-click=\"setOrderBy(field.id)\">\n" +
    "					<span class=\"usin-heading-{{field.id}}\">{{field.name}}</span>\n" +
    "					<span class=\"usin-order-arrow\" ng-class=\"{'usin-order-arrow-up' : userList.order == 'ASC', 'usin-order-arrow-down': userList.order == 'DESC'}\"\n" +
    "						ng-show=\"userList.orderBy==field.id\"></span>\n" +
    "				</span>\n" +
    "			</th>\n" +
    "		</tr>\n" +
    "	</tfoot>\n" +
    "	</table>\n" +
    "	</div>\n" +
    "\n" +
    "	<div class=\"usin-no-results\" ng-show=\"!loading.isLoading() && !userList.users.length\">\n" +
    "		<div class=\"usin-no-results-logo\"></div>\n" +
    "		<h3> {{strings.noResults}}</h3>\n" +
    "	</div>\n" +
    "\n" +
    "	<div class=\"usin-pagination-wrapper\" ng-controller=\"UsinPaginationCtrl\" ng-show=\"pages > 1\">\n" +
    "		<div class=\"usin-pagination\">\n" +
    "			<button class=\"usin-btn usin-pag-btn\" ng-disabled=\"userList.page==1\" ng-click=\"changePage(userList.page-1)\"><span class=\"usin-icon-arrow-left\"></span></button>\n" +
    "			<button class=\"usin-btn usin-pag-btn\" ng-disabled=\"userList.page==pages\" ng-click=\"changePage(userList.page+1)\"><span class=\"usin-icon-arrow-right\"></span></button>\n" +
    "			<span class=\"usin-gotopage\">\n" +
    "			<input type=\"text\" ng-model=\"userPage\" ng-keyup=\"$event.keyCode==13 && changePage(userPage)\">\n" +
    "			{{strings.of}} {{pages}}\n" +
    "		</span>\n" +
    "		</div>\n" +
    "		<div class=\"usin-pagination-circular-loading\" ng-class=\"{'usin-in-loading': loading.isLoading()}\"></div>\n" +
    "		<div class=\"usin-pagination-options\">\n" +
    "			<div>\n" +
    "				<span>{{strings.usersPerPage}}</span>\n" +
    "				<usin-select-field ng-model=\"$parent.userList.usersPerPage\" options=\"pageOptions\" ng-change=\"onUsersPerPageChange()\"></usin-select-field>\n" +
    "			</div>\n" +
    "		</div>\n" +
    "	</div>\n" +
    "\n" +
    "	<div ng-class=\"{'usin-in-loading': loading.isLoading() && !userList.users.length}\">\n" +
    "		<div class=\"usin-loading\"> <span class=\"usin-loading-dot\"></span><span class=\"usin-loading-dot usna-dot2\"></span></div>\n" +
    "	</div>\n" +
    "</div>"
  );


  $templateCache.put('views/user-list/main.html',
    "<div>\n" +
    "	<div usin-filter fields=\"filterFields\" filters=\"filters\" loading=\"loading\" broadcast-change=\"applyFilters()\"></div>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"usin-error\" ng-show=\"error.msg\">\n" +
    "	{{strings.error}}: {{error.msg}}\n" +
    "	<br><span ng-bind-html=\"strings.errorTip\" ng-if=\"listView\"></span>\n" +
    "	<div class=\"usin-error-data\" ng-if=\"error.info\">\n" +
    "		<button class=\"usin-btn-small\" ng-click=\"error.infoVisible = !error.infoVisible\">\n" +
    "			{{ error.infoVisible ? strings.hideDebugInfo : strings.showDebugInfo }}\n" +
    "		</button>\n" +
    "		<pre class=\"usin-debug-info\" ng-show=\"error.infoVisible\" ng-bind-html=\"error.info\"></pre>\n" +
    "	</div>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"usin-float-right usin-options-menu\">\n" +
    "	<div class=\"usin-circular-loading\" ng-class=\"{'usin-in-loading': loading.isLoading() && (total.current || !listView)}\"></div>\n" +
    "	<div class=\"usin-list-options\"></div>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"usin-total\">\n" +
    "	<!-- show if in list view and (when it's loading, but total is > 0 (so it doesn't blink between filters) or when it's not loading, but total is 0) -->\n" +
    "	<span class=\"usin-list-total\" ng-show=\"listView && (!loading.isLoading() || total.all)\">\n" +
    "		<span class=\"usin-icon-people\"></span>\n" +
    "		<span class=\"usin-total-current-number\" ng-show=\"total.current !== total.all\">\n" +
    "			{{ total.current }} / \n" +
    "		</span>\n" +
    "		<span class=\"usin-total-number\" >\n" +
    "			{{ total.all }} \n" +
    "		</span> \n" +
    "		<span>{{strings.users}}</span>\n" +
    "	</span>\n" +
    "	<span class=\"usin-map-total\" ng-show=\"!listView && total.map!==null\">\n" +
    "		<span class=\"usin-icon-map\"></span>\n" +
    "		<span class=\"usin-map-total-number\">{{total.map}}</span>\n" +
    "		<span>{{ strings.mapUsersDetected }}</span>\n" +
    "	</span>\n" +
    "</div>\n" +
    "<div class=\"clear\"></div>\n" +
    "\n" +
    "\n" +
    "<div class=\"usin-map-view\" ng-if=\"!listView\" ng-controller=\"UsinMapCtrl\">\n" +
    "	<div usin-map id=\"usin-list-map\" map-options=\"mapOptions\"></div>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"usin-list-view\" ng-if=\"listView\"></div>\n" +
    "\n" +
    "\n"
  );


  $templateCache.put('views/user-list/profile-activity-dialog.html',
    "<md-dialog aria-label=\"{{title}}\">\n" +
    "	<md-toolbar>\n" +
    "		<div class=\"md-toolbar-tools\">\n" +
    "			<h2>{{title}}</h2>\n" +
    "			<span flex></span>\n" +
    "			<md-button class=\"md-icon-button\" ng-click=\"closeDialog()\">\n" +
    "				<md-icon class=\"usin-icon-delete\" aria-label=\"{{strings.close}}\"></md-icon>\n" +
    "			</md-button>\n" +
    "		</div>\n" +
    "	</md-toolbar>\n" +
    "\n" +
    "	<md-dialog-content>\n" +
    "		<div class=\"md-dialog-content\">\n" +
    "			<div class=\"usin-error\" ng-if=\"error\">{{error.msg}}</div>\n" +
    "			<div class=\"usin-icon-simple-loading\" ng-show=\"loading\"></div>\n" +
    "			<div ng-if=\"items.length\">\n" +
    "				<table class=\"usin-table usin-profile-activity-table\">\n" +
    "					<thead>\n" +
    "						<tr>\n" +
    "							<th ng-repeat=\"prop in itemProps\">{{prop}}</th>\n" +
    "						</tr>\n" +
    "					</thead>\n" +
    "					<tr ng-repeat=\"item in items\">\n" +
    "						<td ng-repeat=\"(key, prop) in itemProps\">\n" +
    "							<a ng-if=\"key=='title' && item.link\" ng-href=\"{{item.link}}\" target=\"_blank\">{{item[key]}}</a>\n" +
    "							<span ng-if=\"!(key=='title' && item.link)\">{{item[key]}}</span>\n" +
    "						</td>\n" +
    "					</tr>\n" +
    "				</table>\n" +
    "			</div>\n" +
    "		</div>\n" +
    "	</md-dialog-content>\n" +
    "</md-dialog>"
  );


  $templateCache.put('views/user-list/profile-editable-field.html',
    "<div ng-class=\"['usin-editable-field', {'usin-field-editing': editing}]\">\n" +
    "	<span class=\"field-name\">{{field.name}}: </span>\n" +
    "	<span class=\"field-value\" ng-hide=\"editing\">{{user[field.id] | optionKeyToVal:field.options || '-'}}</span>\n" +
    "	\n" +
    "	<span ng-if=\"canUpdateUsers\">\n" +
    "		<input type=\"text\" ng-if=\"field.type=='text' || field.type=='date'\" ng-model=\"user[field.id]\" ng-show=\"editing\" ng-keyup=\"$event.keyCode==13 && updateField()\">\n" +
    "		<input type=\"number\" usin-string-to-number ng-if=\"field.type=='number'\" ng-model=\"user[field.id]\" ng-show=\"editing\" ng-keyup=\"$event.keyCode==13 && updateField()\">\n" +
    "		<span ng-if=\"field.type=='select'\" ng-show=\"editing\" class=\"usin-editable-select-wrap\">\n" +
    "			<div class=\"usin-profile-select-wrap\">\n" +
    "				<usin-select-field ng-model=\"user[field.id]\" options=\"field.options\" ng-keyup=\"$event.keyCode==13 && updateField()\"></usin-select-field>\n" +
    "				<div class=\"usin-btn-close usin-icon-close\" ng-click=\"clearSelection()\">\n" +
    "					<md-tooltip md-direction=\"top\">{{strings.clearSelection}}</md-tooltip>\n" +
    "				</div>\n" +
    "			</div>\n" +
    "		</span>\n" +
    "		\n" +
    "		<div class=\"usin-btn-edit usin-icon-edit alignright\" ng-click=\"toggleEdit()\" ng-show=\"!editing && !settings.editing\"></div>\n" +
    "		<div ng-class=\"['usin-btn-apply', 'alignright', {'usin-icon-apply':!loading, 'usin-icon-simple-loading':loading}]\" ng-click=\"updateField()\" ng-show=\"editing\">\n" +
    "			<md-tooltip md-direction=\"top\">{{strings.saveChanges}}</md-tooltip>\n" +
    "		</div>\n" +
    "		<div class=\"usin-error\" ng-show=\"errorMsg\">{{errorMsg}}</div>\n" +
    "	</span>\n" +
    "	<div class=\"clear\"></div>\n" +
    "</div>"
  );


  $templateCache.put('views/user-list/profile-groups.html',
    "<div ng-class=\"['usin-profile-groups-wrapper', {'usin-field-editing': editing}]\">\n" +
    "	<div>\n" +
    "		<span class=\"field-name\">{{strings.groups}}:</span> {{userGroupNames()}}\n" +
    "		<span ng-show=\"!user.user_groups.length\">-</span>\n" +
    "		<span ng-repeat=\"groupId in user.user_groups\" ng-bind-html=\"groupId | groupTagHtml\"></span>\n" +
    "		<span ng-if=\"canUpdateUsers\">\n" +
    "			<div class=\"usin-btn-edit usin-icon-edit alignright\" ng-click=\"toggleEdit()\" ng-show=\"!editing && allGroups.length && !settings.editing\"></div>\n" +
    "			<div class=\"usin-btn-apply usin-icon-apply alignright\" ng-click=\"updateGroups()\" ng-show=\"!groupLoading && editing\"></div>\n" +
    "			<div class=\"usin-icon-simple-loading usin-group-loading alignright\" ng-show=\"groupLoading\"></div>\n" +
    "			<div class=\"usin-groups-list\" ng-show=\"editing\">\n" +
    "				<div class=\"usin-error\" ng-show=\"groupErrorMsg\">{{groupErrorMsg}}</div>\n" +
    "				<ul>\n" +
    "					<li ng-repeat=\"group in allGroups\">\n" +
    "						<md-checkbox ng-checked=\"userHasGroup(group.key)\" md-no-ink=\"true\"\n" +
    "							aria-label=\"Toggle Group {{group.val}}\" ng-click=\"toggleGroup(group.key)\"></md-checkbox>\n" +
    "						<span>{{group.val}}</span>\n" +
    "					</li>\n" +
    "				</ul>\n" +
    "			</div>\n" +
    "		</span>\n" +
    "	</div>\n" +
    "</div>"
  );


  $templateCache.put('views/user-list/profile-notes.html',
    "<div ng-class=\"['usin-notes', {'usin-notes-empty': !user.notes || !user.notes.length}]\">\n" +
    "	<h3 class=\"usin-profile-title\">{{strings.notes}}</h3>\n" +
    "	\n" +
    "	<div class=\"usin-notes-form\" ng-show=\"canUpdateUsers\">\n" +
    "		<textarea ng-model=\"noteContent\" class=\"usin-note-field\" rows=\"3\"></textarea>\n" +
    "		<span class=\"usin-btn usin-btn-main usin-btn-note\" ng-click=\"addNote()\">{{strings.addNote}}</span>\n" +
    "		<div class=\"usin-icon-simple-loading usin-note-loading alignright\" ng-show=\"noteLoading\"></div>\n" +
    "	</div>\n" +
    "	\n" +
    "	<div class=\"clear\"></div>\n" +
    "	<div class=\"usin-error\" ng-show=\"noteErrorMsg\">{{noteErrorMsg}}</div>\n" +
    "	\n" +
    "	<div ng-if=\"user.notes\" class=\"usin-notes-list\">\n" +
    "		<div ng-repeat=\"(index, note) in user.notes\" ng-class=\"['usin-note', 'usin-note-'+note.state]\">\n" +
    "			<div class=\"usin-note-content\">{{note.content}}</div>\n" +
    "			<div class=\"usin-note-info\">{{strings.by}} {{note.by}} | {{note.date}}\n" +
    "			<span class=\"alignright usin-note-delete\" ng-if=\"canUpdateUsers\" usin-confirmed-click=\"deleteNote(note.id, index)\" usin-confirm-click=\"{{strings.areYouSure}}\">{{strings.delete}}</span>\n" +
    "			<span class=\"usin-custom-directive\" ng-repeat=\"ct in customTemplates['note_actions']\" ct=\"ct\" ></span>\n" +
    "			</div>\n" +
    "			\n" +
    "		</div>\n" +
    "	</div>\n" +
    "	\n" +
    "	<div class=\"usin-custom-directive\" ng-repeat=\"ct in customTemplates['after_notes']\" ct=\"ct\" ></div>\n" +
    "</div>"
  );


  $templateCache.put('views/user-list/profile.html',
    "<div class=\"usin-profile\">\n" +
    "\n" +
    "	<div class=\"usin-profile-buttons\">\n" +
    "		<a class=\"usin-btn usin-profile-back-btn\" href=\"#/\"><span class=\"usin-icon-arrow-left\"></span> {{strings.back}}</a>\n" +
    "\n" +
    "		<div class=\"usin-profile-actions\" ng-if=\"user.actions.length\">\n" +
    "			<a ng-repeat=\"action in user.actions\" href=\"{{action.link}}\" target=\"_blank\" class=\"usin-btn\">\n" +
    "				<span class=\"usin-icon-{{action.id}}\"></span>\n" +
    "				<md-tooltip md-direction=\"top\" ng-if=\"action.name\">{{action.name}}</md-tooltip>\n" +
    "			</a>\n" +
    "		</div>\n" +
    "\n" +
    "		<div class=\"usin-profile-settings-menu\">\n" +
    "			<span class=\"usin-error\" ng-if=\"settings.error\">{{settings.error}}</span>\n" +
    "			<button ng-class=\"['usin-btn', 'usin-btn-edit-profile-settings', {'usin-btn-main': settings.editing}]\" ng-click=\"toggleEditSettings()\">\n" +
    "				<span ng-class=\"[{'usin-icon-settings':!settings.editing, 'usin-icon-apply': settings.editing}]\"></span>\n" +
    "				<span ng-show=\"settings.editing\">{{strings.saveChanges}}</span>\n" +
    "				<md-tooltip md-direction=\"top\" ng-if=\"!settings.editing\">{{strings.profileSettings}}</md-tooltip>\n" +
    "			</button>\n" +
    "			<div class=\"usin-circular-loading\" ng-class=\"{'usin-in-loading': settings.loading}\"></div>\n" +
    "		</div>\n" +
    "	</div>\n" +
    "\n" +
    "	<div ng-show=\"loading\" class=\"usin-in-loading\">\n" +
    "		<div class=\"usin-loading\"> <span class=\"usin-loading-dot\"></span><span class=\"usin-loading-dot usna-dot2\"></span></div>\n" +
    "	</div>\n" +
    "\n" +
    "	<div class=\"usin-error\" ng-show=\"error.msg\">\n" +
    "		{{strings.error}}: {{error.msg}}\n" +
    "		<div class=\"usin-error-data\" ng-if=\"error.info\">\n" +
    "			<button class=\"usin-btn-small\" ng-click=\"error.infoVisible = !error.infoVisible\">\n" +
    "				{{ error.infoVisible ? strings.hideDebugInfo : strings.showDebugInfo }}\n" +
    "			</button>\n" +
    "			<pre class=\"usin-debug-info\" ng-show=\"error.infoVisible\" ng-bind-html=\"error.info\"></pre>\n" +
    "		</div>\n" +
    "	</div>\n" +
    "\n" +
    "<div ng-show=\"user && !loading\" ng-class=\"['usin-user-profile-container', {'usin-profile-settings-editing': settings.editing}]\" >\n" +
    "	<div ng-class=\"['usin-user-profile-wrap', 'usin-one-third', 'usin-column', {'usin-user-has-map':mapOptions}]\">\n" +
    "		<div class=\"usin-user-profile\">\n" +
    "			<div class=\"usin-profile-map-wrapper\" ng-if=\"mapOptions\">\n" +
    "		  		<div usin-map id=\"usin-profile-map\" map-options=\"mapOptions\"></div>\n" +
    "			</div>\n" +
    "		<div class=\"usin-avatar\" ng-bind-html=\"user.avatar\"></div>\n" +
    "\n" +
    "		<div class=\"usin-personal-data\">\n" +
    "			<div ng-repeat=\"field in personalFields\" class=\"usin-profile-field-{{field['id']}}\">\n" +
    "				<span class=\"field-name\">{{field.name}}:</span><span class=\"field-value\"> <h3>{{user[field['id']]}}</h3></span>\n" +
    "			</div> \n" +
    "		</div>\n" +
    "        <div class=\"clear\"></div>\n" +
    "		<div class=\"usin-general-data\">\n" +
    "\n" +
    "			<!-- GROUPS -->\n" +
    "			<div usin-profile-groups></div>\n" +
    "			\n" +
    "			<ul dnd-list=\"generalFields\" class=\"usin-general-fields-list\">\n" +
    "			<li ng-repeat=\"field in generalFields\" \n" +
    "				ng-class=\"['usin-profile-field', 'usin-profile-field-'+field.id, {'usin-profile-field-empty': !user[field.id]}]\"\n" +
    "				dnd-draggable=\"field\" dnd-moved=\"reorderFields($index)\" dnd-disable-if=\"!settings.editing\" ng-hide=\"field.isMissing\">\n" +
    "				<span class=\"usin-profile-field-wrap\">\n" +
    "\n" +
    "					<div ng-class=\"['usin-field-content', {'usin-profile-field-hidden': field.hide}]\" ng-hide=\"field.hide && !settings.editing\">\n" +
    "						<!--SEPARATORS: -->\n" +
    "						<div ng-if=\"field.isSeparator\" class=\"usin-profile-separator\"><span>{{field.text}}</span></div>\n" +
    "\n" +
    "						<!-- NON-EDITABLE FIELDS: -->\n" +
    "						<div ng-if=\"field.isStandardField\">\n" +
    "							<span class=\"field-name\">{{field.name}}: </span>\n" +
    "							<span class=\"field-value\">{{user[field.id]}}</span>\n" +
    "						</div>\n" +
    "						<!-- EDITABLE FIELDS: -->\n" +
    "						<div usin-profile-editable-field ng-if=\"field.isEditableField\"></div>\n" +
    "					</div>\n" +
    "\n" +
    "					<div class=\"usin-profile-settings-actions\" ng-if=\"settings.editing\">\n" +
    "						<div ng-if=\"!field.isSeparator\" ng-class=\"['usin-profile-hide-icon', 'usin-icon-visible', {'usin-icon-visible-off': field.hide}]\"\n" +
    "							ng-click=\"toggleFieldVisibility(field)\"></div>\n" +
    "\n" +
    "						<div ng-if=\"field.isSeparator\" class=\"usin-icon-close usin-profile-remove-icon\" ng-click=\"removeSeparator(field)\"></div>\n" +
    "\n" +
    "						<div dnd-handle class=\"usin-drag-handle usin-icon-sort\" ng-if=\"settings.editing\"></div>\n" +
    "					</div>\n" +
    "		\n" +
    "				</span>\n" +
    "			</li> \n" +
    "			<li class=\"dndPlaceholder\"><label></label></li>\n" +
    "\n" +
    "		</ul>\n" +
    "\n" +
    "		<usin-add-separator ng-if=\"settings.editing\" on-add-separator=\"addSeparator(title)\"></usin-add-separator>\n" +
    "\n" +
    "		<span ng-show=\"hiddenFieldsCount && !settings.editing\" class=\"usin-profile-hidden-items-count\">\n" +
    "			( {{hiddenFieldsCount}} {{strings.hiddenItems}} )\n" +
    "			<md-tooltip md-direction=\"right\">{{strings.profileHiddenItemsInstructions}}</md-tooltip>\n" +
    "		</span>\n" +
    "\n" +
    "		</div>\n" +
    "\n" +
    "	</div>\n" +
    "</div>\n" +
    "\n" +
    "<div class=\"usin-user-data-wrap usin-one-third usin-column\">\n" +
    "	<div class=\"usin-user-data\">\n" +
    "		<div class=\"usin-activity\">\n" +
    "			<h3 class=\"usin-profile-title\">{{strings.activity}}</h3>\n" +
    "			<ul ng-if=\"activityItems.length\" dnd-list=\"activityItems\">\n" +
    "				<li ng-repeat=\"item in activityItems\" ng-init=\"userActivity = getUserActivity(item.id)\" class=\"usin-activity-item\" ng-hide=\"item.isMissing\"\n" +
    "					dnd-draggable=\"item\" dnd-moved=\"reorderActivity($index)\" dnd-disable-if=\"!settings.editing\">\n" +
    "\n" +
    "					<div ng-class=\"['usin-activity-content', {'usin-activity-hidden': item.hide}]\" ng-hide=\"item.hide && !settings.editing\">\n" +
    "						<div class=\"usin-activity-title-wrap\">\n" +
    "							<h4 class=\"usin-act-title\">\n" +
    "								<span ng-class=\"['usin-act-icon', {'usin-icon-{{userActivity.icon}}':userActivity.icon, 'usin-icon-field': !userActivity.icon}]\"></span>\n" +
    "								<span ng-if=\"!userActivity.hide_count\">\n" +
    "									{{userActivity.count}}\n" +
    "								</span>\n" +
    "								{{userActivity.label}}\n" +
    "							</h4>\n" +
    "\n" +
    "							<div class=\"usin-profile-settings-actions\" ng-if=\"settings.editing\">\n" +
    "								<div ng-class=\"['usin-profile-hide-icon', 'usin-icon-visible', {'usin-icon-visible-off': item.hide}]\"\n" +
    "									ng-click=\"toggleActivityVisibility(item)\"></div>\n" +
    "\n" +
    "								<div dnd-handle class=\"usin-drag-handle usin-icon-sort\" ng-if=\"settings.editing\"></div>\n" +
    "							</div>\n" +
    "						</div>\n" +
    "\n" +
    "						<ul ng-if=\"userActivity.list.length\" class=\"usin-activity-list\">\n" +
    "							<li ng-repeat=\"listItem in userActivity.list\">\n" +
    "								<span class=\"usin-icon-list\"></span>\n" +
    "								<a class=\"usin-user-activity-item-title\" ng-href=\"{{listItem.link}}\" target=\"_blank\" ng-bind-html=\"listItem.title\" ng-if=\"listItem.link\"></a>\n" +
    "								<span class=\"usin-user-activity-item-title\" ng-bind-html=\"listItem.title\" ng-if=\"!listItem.link\"></span>\n" +
    "								<div ng-if=\"listItem.details.length\" ng-repeat=\"details in listItem.details\" ng-bind-html=\"details\" class=\"usin-activity-details\"></div>\n" +
    "							</li>\n" +
    "						</ul>\n" +
    "						<span ng-if=\"userActivity.list.length < userActivity.count && userActivity.link\" class=\"usin-icon-more usin-activity-more\"></span>\n" +
    "						<a class=\"usin-act-more\" ng-href=\"{{userActivity.link}}\" ng-if=\"userActivity.link\" target=\"_blank\">{{strings.view}}</a>\n" +
    "						<a class=\"usin-act-more\" ng-if=\"userActivity.dialog\" ng-click=\"loadAllActivity(userActivity)\">{{strings.viewAll}}</a>\n" +
    "\n" +
    "\n" +
    "						\n" +
    "\n" +
    "					</div>\n" +
    "				</li>\n" +
    "				<li class=\"dndPlaceholder\"><label></label></li>\n" +
    "			</ul>\n" +
    "			<span ng-if=\"!user.activity.length\">\n" +
    "				{{strings.noActivity}}\n" +
    "			</span>\n" +
    "			<span ng-show=\"hiddenActivityCount && !settings.editing\" class=\"usin-profile-hidden-items-count\">\n" +
    "				( {{hiddenActivityCount}} {{strings.hiddenItems}} )\n" +
    "				<md-tooltip md-direction=\"top\">{{strings.profileHiddenItemsInstructions}}</md-tooltip>\n" +
    "			</span>\n" +
    "		</div>\n" +
    "	</div>\n" +
    "	</div>\n" +
    "	\n" +
    "	<div class=\"usin-user-notes-wrap usin-one-third usin-column\">\n" +
    "		<!-- NOTES -->\n" +
    "		<div usin-profile-notes></div>\n" +
    "	</div>\n" +
    "	\n" +
    "</div>\n" +
    "\n" +
    "</div>\n"
  );


  $templateCache.put('views/user-list/segments.html',
    "<div class=\"usin-segments-wrap\">\n" +
    "	<button class=\"usin-btn\" ng-click=\"toggleOptions()\" ng-class=\"{'usin-btn-drop-down-opened' : optionsVisible === true}\"\n" +
    "		ng-disabled=\"!listView || bulkActions.isAnyChecked() || loading.isLoading()\">\n" +
    "		<span class=\"usin-icon-segment\"/>\n" +
    "		<span class=\"usin-icon-drop-down usin-btn-drop-down\"></span>\n" +
    "		<md-tooltip md-direction=\"top\">{{strings.segments}}</md-tooltip>\n" +
    "	</button>\n" +
    "	\n" +
    "	<div ng-if=\"optionsVisible\" class=\"usin-drop-down usin-segments-options usin-animate ng-hide\" ng-show=\"optionsVisible\" click-outside=\"optionsVisible=false\">\n" +
    "		<ul class=\"usin-segments-list\">\n" +
    "			<li class=\"usin-create-segment-wrapper\" ng-if=\"canManageSegments\">\n" +
    "				<md-tooltip md-direction=\"top\" md-autohide>{{canCreateSegment() ? strings.saveSegmentTooltip : strings.disabledSegmentTooltip}}</md-tooltip>\n" +
    "				<button class=\"usin-save-segment usin-btn-small usin-btn-main\" ng-click=\"openSegmentDialog()\" ng-disabled=\"!canCreateSegment()\">\n" +
    "					<span class=\"usin-icon-add\"></span>\n" +
    "					{{strings.newSegment}}\n" +
    "				</button>\n" +
    "			</li>\n" +
    "			<li ng-repeat=\"segment in segments\">\n" +
    "				<span class=\"usin-icon-segment\"></span>\n" +
    "				<span class=\"usin-segment-name\" ng-click=\"applySegment(segment)\">{{segment.name}}</span>\n" +
    "				<span class=\"usin-icon-close usin-float-right\" ng-click=\"deleteSegment(segment)\" ng-if=\"canManageSegments\"></span>\n" +
    "			</li>\n" +
    "		</ul>\n" +
    "  	</div>\n" +
    "	\n" +
    "</div>\n"
  );

}]);
