angular.module('usinCustomFieldsApp').run(['$templateCache', function($templateCache) {
  'use strict';

  $templateCache.put('views/custom-fields/field-form.html',
    "<div class=\"usin-columns\">\n" +
    "<div class=\"usin-custom-fields-form-element usin-column usin-one-third\">\n" +
    "	<label>{{strings.fieldName}}</label>\n" +
    "	<input type=\"text\" name=\"usin-field-name\" ng-model=\"field.name\"/>\n" +
    "</div>\n" +
    "<div class=\"usin-custom-fields-form-element usin-column usin-one-third\">\n" +
    "	<label>{{strings.fieldKey}}</label>\n" +
    "	<input type=\"text\" name=\"usin-field-key\" ng-model=\"field.key\" ng-disabled=\"mode == 'edit'\"/>\n" +
    "</div>\n" +
    "<span ng-if=\"mode=='add'\" class=\"usin-custom-directive\" ng-repeat=\"ct in customTemplates['after_key_field']\" ct=\"ct\" ></span>\n" +
    "<div class=\"usin-custom-fields-form-element usin-column usin-one-third\">\n" +
    "	<label>{{strings.fieldType}}</label>\n" +
    "	<usin-select-field ng-model=\"field.type\" options=\"fieldTypes\" option-key=\"type\" option-val=\"name\"></usin-select-field>\n" +
    "</div>\n" +
    "</div>\n" +
    "<br>\n" +
    "<div class=\"usin-custom-fields-form-element usin-custom-fields-dropdown-options\" ng-if=\"field.type=='select'\">\n" +
    "	<div class=\"usin-columns\">\n" +
    "		<div class=\"usin-column usin-two-thirds\">\n" +
    "			<label>{{strings.fieldOptions}}</label>\n" +
    "			<textarea class=\"usin-textarea\" ng-model=\"field.options\"></textarea>\n" +
    "		</div>\n" +
    "		<div class=\"usin-column usin-one-third\">\n" +
    "			<p ng-bind-html=\"strings.fieldOptionsInfo\" class=\"usin-message\"></p>\n" +
    "		</div>\n" +
    "	</div>\n" +
    "</div>"
  );


  $templateCache.put('views/custom-fields/field-row.html',
    "<td ng-if=\"!editing\">\n" +
    "	<span>{{field.name}}</span>\n" +
    "</td>\n" +
    "<td ng-if=\"!editing\">\n" +
    "	<span>{{field.key}}</span>\n" +
    "</td>\n" +
    "<td ng-if=\"!editing\">\n" +
    "	<span>{{getTypeName(field.type)}}</span>\n" +
    "</td>\n" +
    "\n" +
    "<td ng-if=\"editing\" colspan=\"3\">\n" +
    "	<usin-field-form field=\"field\" mode=\"edit\"></usin-field-form>\n" +
    "</td>\n" +
    "\n" +
    "<td class=\"usin-table-actions\">\n" +
    "	\n" +
    "	<div class=\"usin-action-wrapper\">\n" +
    "		<div class=\"usin-btn-edit usin-icon-edit\" ng-click=\"toggleEdit()\" ng-show=\"!editing\">\n" +
    "			<md-tooltip md-direction=\"top\">{{strings.edit}}</md-tooltip>\n" +
    "		</div>\n" +
    "		<span class=\"usin-btn-delete usin-icon-delete\" usin-confirmed-click=\"deleteField()\" usin-confirm-click=\"{{strings.areYouSure}}\" ng-show=\"!editing\">\n" +
    "			<md-tooltip md-direction=\"top\">{{strings.delete}}</md-tooltip>\n" +
    "		</span>\n" +
    "		<div class=\"usin-btn-apply usin-icon-apply\" ng-click=\"updateField()\" ng-show=\"editing\">\n" +
    "			<md-tooltip md-direction=\"top\">{{strings.saveChanges}}</md-tooltip>\n" +
    "		</div>\n" +
    "		<span class=\"usin-icon-simple-loading alignright\" ng-show=\"loading\"></span>\n" +
    "		<div class=\"usin-error\" ng-if=\"errorMsg\">{{errorMsg}}</div>\n" +
    "	</div>\n" +
    "	\n" +
    "</td>"
  );


  $templateCache.put('views/custom-fields/main.html',
    "<div class=\"usin-custom-fields-wrap\">\n" +
    "	<h3>{{$ctrl.strings.addField}}</h3>\n" +
    "\n" +
    "	<div class=\"usin-custom-fields-form\">\n" +
    "		<usin-field-form field=\"$ctrl.field\" mode=\"add\"></usin-field-form>\n" +
    "		<div class=\"usin-btn usin-btn-main\" ng-click=\"$ctrl.addField()\">{{$ctrl.strings.addField}}</div>\n" +
    "		<div class=\"usin-error\" ng-show=\"$ctrl.errorMsg\">{{$ctrl.errorMsg}}</div>\n" +
    "		<span class=\"usin-icon-simple-loading\" ng-show=\"$ctrl.loading\"></span>\n" +
    "	</div>\n" +
    "\n" +
    "	<div class=\"usin-message\">{{$ctrl.strings.keyMessage}}</div>\n" +
    "\n" +
    "\n" +
    "\n" +
    "	<div ng-if=\"$ctrl.fields.length\">\n" +
    "			<h3>{{$ctrl.strings.fields}}</h3>\n" +
    "			\n" +
    "			<div class=\"usin-table-wrap\">\n" +
    "				<table class=\"usin-table\">\n" +
    "					<thead>\n" +
    "						<tr>\n" +
    "							<th>{{$ctrl.strings.fieldName}}</th>\n" +
    "							<th>{{$ctrl.strings.fieldKey}}</th>\n" +
    "							<th>{{$ctrl.strings.fieldType}}</th>\n" +
    "							<th>{{$ctrl.strings.actions}}</th>\n" +
    "						</tr>\n" +
    "					</thead>\n" +
    "					<tr usin-field-row ng-repeat=\"f in $ctrl.fields\" field=\"f\" on-fields-change=\"$ctrl.doOnFieldsChange(fields)\"></tr>\n" +
    "				\n" +
    "				</table>\n" +
    "			</div>\n" +
    "		</div>\n" +
    "</div>"
  );

}]);
