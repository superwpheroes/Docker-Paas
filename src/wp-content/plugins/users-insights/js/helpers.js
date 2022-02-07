var usinHelpers = {

	config: {
		animateProvider: [ '$animateProvider', function(  $animateProvider){
			//apply animation to all elements with class that contains md- (usually Angular Material classes)
			//or elements that have the usin-animate class explicitly
			//this is done to prevent applying flickering animations to all elements that
			//use the ng-show or ng-hide directives
			$animateProvider.classNameFilter(/((^md-)|(usin-animate)).*/);
			// $animateProvider.classNameFilter(/^(?!usin).+/);
			// $animateProvider.classNameFilter(/^((?!(ng-animate-disabled)).)*$/);
		}],
		
		mdThemingProvider: ['$mdThemingProvider', function ($mdThemingProvider) {
			$mdThemingProvider.theme('usin')
				.primaryPalette('teal')
				.accentPalette('blue-grey');
			$mdThemingProvider.setDefaultTheme('usin');
		}],

		httpProvider: ['$httpProvider', function($httpProvider){
			// Use x-www-form-urlencoded Content-Type
			$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
		
			// Override $http service's default transformRequest
			$httpProvider.defaults.transformRequest = [function(data) {
				return angular.isObject(data) && String(data) !== '[object File]' ? usinHelpers.serializeObject(data) : data;
			}];
			// we can also use:
			// $httpProvider.defaults.paramSerializer = $httpParamSerializerJQLike;
		}],
		mdDateLocaleProvider: ['$mdDateLocaleProvider', function($mdDateLocaleProvider){
			$mdDateLocaleProvider.formatDate = function(date) {
				if(!date){
					return date;
				}
				var months = window.USIN.months;
				return months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear();
			};
		}]
	},

	/**
	* Converts an object to x-www-form-urlencoded serialization.
	* @param {Object} obj
	* @return {String}
	*/
	serializeObject : function(obj){
		var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

		for(name in obj) {
			value = obj[name];

			if(value instanceof Array) {
				for(i=0; i<value.length; ++i) {
					subValue = value[i];
					fullSubName = name + '[' + i + ']';
					innerObj = {};
					innerObj[fullSubName] = subValue;
					query += this.serializeObject(innerObj) + '&';
				}
			}
			else if(value instanceof Object) {
				for(subName in value) {
					subValue = value[subName];
					fullSubName = name + '[' + subName + ']';
					innerObj = {};
					innerObj[fullSubName] = subValue;
					query += this.serializeObject(innerObj) + '&';
				}
			}
			else if(value !== undefined && value !== null)
			query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
		}

		return query.length ? query.substr(0, query.length - 1) : query;
	},

	url : {
		addParam : function(url, key, val){
			if (url.indexOf(key + "=") >= 0){
				var prefix = url.substring(0, url.indexOf(key));
				var suffix = url.substring(url.indexOf(key));
				suffix = suffix.substring(suffix.indexOf("=") + 1);
				suffix = (suffix.indexOf("&") >= 0) ? suffix.substring(suffix.indexOf("&")) : "";
				url = prefix + key + "=" + val + suffix;
			}else if (url.indexOf("?") < 0){
				url += "?" + key + "=" + val;
			}else{
				url += "&" + key + "=" + val;
			}
			return url;
		},

		addParams : function(url, params){
			for(var i in params){
				if(params.hasOwnProperty(i)){
					url = this.addParam(url, i, params[i]);
				}
			}
			return url;
		}
	},

	session: {
		get : function(key){
			if(!sessionStorage) return null;

			return sessionStorage.getItem(this._key(key));
		},
		set : function(key, val){
			if(!sessionStorage) return;

			sessionStorage.setItem(this._key(key), val);
		},
		remove: function(key){
			if(!sessionStorage) return;

			sessionStorage.removeItem(this._key(key));
		},
		_key : function(key){
			return "_usin"+key;
		}
	}
};

//helper module
usinHelpers.ngModule = angular.module("usinNgHelpers" , []);

usinHelpers.ngModule.directive('usinConfirmClick', function(){
	return {
		link: function ($scope, $element, $attrs) {
			var msg = $attrs.usinConfirmClick,
				clickAction = $attrs.usinConfirmedClick;
			$element.bind('click',function (event) {
				if ( window.confirm(msg) ) {
					$scope.$eval(clickAction);
				}
			});
		}
	};
})
.directive('usinStringToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      ngModel.$formatters.push(function(value) {
        return parseFloat(value, 10);
      });
    }
  };
})
.directive('usinCustomDirective', function(){
	return {
		restrict: 'EAC',
		template: function(element, attrs) {
			return '<span ng-include="'+attrs.ct+'"></span>';
		}
	};
})
/**
 * Code from: https://github.com/IamAdamJowett/angular-click-outside
 * @ngdoc directive
 * @name angular-click-outside.directive:clickOutside
 * @description Directive to add click outside capabilities to DOM elements
 *
 * The MIT License (MIT)
 * Copyright (c) 2015 Adam Jowett
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 **/
.directive('clickOutside', ['$document', '$parse', '$timeout',
    function clickOutside($document, $parse, $timeout) {
    return {
        restrict: 'A',
        link: function($scope, elem, attr) {

            // postpone linking to next digest to allow for unique id generation
            $timeout(function() {
                var classList = (attr.outsideIfNot !== undefined) ? attr.outsideIfNot.split(/[ ,]+/) : [],
                    fn;

                function eventHandler(e) {
                    var i, element, r, id, classNames, l;

                    // check if our element already hidden and abort if so
                    if (angular.element(elem).hasClass("ng-hide")) {
                        return;
                    }

                    // if there is no click target, no point going on
                    if (!e || !e.target) {
                        return;
                    }

                    // loop through the available elements, looking for classes in the class list that might match and so will eat
                    for (element = e.target; element; element = element.parentNode) {
                        // check if the element is the same element the directive is attached to and exit if so (props @CosticaPuntaru)
                        if (element === elem[0]) {
                            return;
                        }
                        
                        // now we have done the initial checks, start gathering id's and classes
                        id = element.id,
                        classNames = element.className,
                        l = classList.length;

                        // Unwrap SVGAnimatedString classes
                        if (classNames && classNames.baseVal !== undefined) {
                            classNames = classNames.baseVal;
                        }

                        // if there are no class names on the element clicked, skip the check
                        if (classNames || id) {

                            // loop through the elements id's and classnames looking for exceptions
                            for (i = 0; i < l; i++) {
                                //prepare regex for class word matching
                                r = new RegExp('\\b' + classList[i] + '\\b');

                                // check for exact matches on id's or classes, but only if they exist in the first place
                                if ((id !== undefined && id === classList[i]) || (classNames && r.test(classNames))) {
                                    // now let's exit out as it is an element that has been defined as being ignored for clicking outside
                                    return;
                                }
                            }
                        }
                    }

                    // if we have got this far, then we are good to go with processing the command passed in via the click-outside attribute
                    $timeout(function() {
                        fn = $parse(attr['clickOutside']);
                        fn($scope, { event: e });
                    });
                }

                // if the devices has a touchscreen, listen for this event
                if (_hasTouch()) {
                    $document.on('touchstart', eventHandler);
                }

                // still listen for the click event even if there is touch to cater for touchscreen laptops
                $document.on('click', eventHandler);

                // when the scope is destroyed, clean up the documents event handlers as we don't want it hanging around
                $scope.$on('$destroy', function() {
                    if (_hasTouch()) {
                        $document.off('touchstart', eventHandler);
                    }

                    $document.off('click', eventHandler);
                });

                /**
                 * @description Private function to attempt to figure out if we are on a touch device
                 * @private
                 **/
                function _hasTouch() {
                    // works on most browsers, IE10/11 and Surface
                    return 'ontouchstart' in window || navigator.maxTouchPoints;
                };
            });
        }
    };
}]);
