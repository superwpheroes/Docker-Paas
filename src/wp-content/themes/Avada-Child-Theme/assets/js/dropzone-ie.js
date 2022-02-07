function daDA() {
    return 20 - document.getElementsByClassName("um-gallery-item").length
}
(function () {
    var e, t, i, n, a, r, o, s, l = [].slice, c = {}.hasOwnProperty, d = function (e, t) {
        function i() {
            this.constructor = e
        }

        for (var n in t)c.call(t, n) && (e[n] = t[n]);
        return i.prototype = t.prototype, e.prototype = new i, e.__super__ = t.prototype, e
    };
    o = function () {
    }, t = function () {
        function e() {
        }

        return e.prototype.addEventListener = e.prototype.on, e.prototype.on = function (e, t) {
            return this._callbacks = this._callbacks || {}, this._callbacks[e] || (this._callbacks[e] = []), this._callbacks[e].push(t), this
        }, e.prototype.emit = function () {
            var e, t, i, n, a;
            if (i = arguments[0], e = 2 <= arguments.length ? l.call(arguments, 1) : [], this._callbacks = this._callbacks || {}, t = this._callbacks[i])for (n = 0, a = t.length; a > n; n++)t[n].apply(this, e);
            return this
        }, e.prototype.removeListener = e.prototype.off, e.prototype.removeAllListeners = e.prototype.off, e.prototype.removeEventListener = e.prototype.off, e.prototype.off = function (e, t) {
            var i, n, a, r;
            if (!this._callbacks || 0 === arguments.length)return this._callbacks = {}, this;
            if (!(i = this._callbacks[e]))return this;
            if (1 === arguments.length)return delete this._callbacks[e], this;
            for (n = a = 0, r = i.length; r > a; n = ++a)if (i[n] === t) {
                i.splice(n, 1);
                break
            }
            return this
        }, e
    }(), (e = function (e) {
        function i(e, t) {
            var a, r, o;
            if (this.element = e, this.version = i.version, this.defaultOptions.previewTemplate = this.defaultOptions.previewTemplate.replace(/\n*/g, ""), this.clickableElements = [], this.listeners = [], this.files = [], "string" == typeof this.element && (this.element = document.querySelector(this.element)), !this.element || null == this.element.nodeType)throw new Error("Invalid dropzone element.");
            if (this.element.dropzone)throw new Error("Dropzone already attached.");
            if (i.instances.push(this), this.element.dropzone = this, a = null != (o = i.optionsForElement(this.element)) ? o : {}, this.options = n({}, this.defaultOptions, a, null != t ? t : {}), this.options.forceFallback || !i.isBrowserSupported())return this.options.fallback.call(this);
            if (null == this.options.url && (this.options.url = this.element.getAttribute("action")), !this.options.url)throw new Error("No URL provided.");
            if (this.options.acceptedFiles && this.options.acceptedMimeTypes)throw new Error("You can't provide both 'acceptedFiles' and 'acceptedMimeTypes'. 'acceptedMimeTypes' is deprecated.");
            this.options.acceptedMimeTypes && (this.options.acceptedFiles = this.options.acceptedMimeTypes, delete this.options.acceptedMimeTypes), this.options.method = this.options.method.toUpperCase(), (r = this.getExistingFallback()) && r.parentNode && r.parentNode.removeChild(r), !1 !== this.options.previewsContainer && (this.previewsContainer = this.options.previewsContainer ? i.getElement(this.options.previewsContainer, "previewsContainer") : this.element), this.options.clickable && (this.clickableElements = !0 === this.options.clickable ? [this.element] : i.getElements(this.options.clickable, "clickable")), this.init()
        }

        var n, a;
        return d(i, e), i.prototype.Emitter = t, i.prototype.events = ["drop", "dragstart", "dragend", "dragenter", "dragover", "dragleave", "addedfile", "addedfiles", "removedfile", "thumbnail", "error", "errormultiple", "processing", "processingmultiple", "uploadprogress", "totaluploadprogress", "sending", "sendingmultiple", "success", "successmultiple", "canceled", "canceledmultiple", "complete", "completemultiple", "reset", "maxfilesexceeded", "maxfilesreached", "queuecomplete"], i.prototype.defaultOptions = {
            url: null,
            method: "post",
            withCredentials: !1,
            parallelUploads: 2,
            uploadMultiple: !1,
            maxFilesize: 8,
            paramName: "file",
            createImageThumbnails: !0,
            maxThumbnailFilesize: 10,
            thumbnailWidth: 120,
            thumbnailHeight: 120,
            filesizeBase: 1e3,
            maxFiles: daDA(),
            params: {},
            clickable: !0,
            ignoreHiddenFiles: !0,
            acceptedFiles: null,
            acceptedMimeTypes: null,
            autoProcessQueue: !0,
            autoQueue: !0,
            addRemoveLinks: !1,
            previewsContainer: null,
            hiddenInputContainer: "body",
            capture: null,
            renameFilename: null,
            dictDefaultMessage: "Drop files here to upload",
            dictFallbackMessage: "Your browser does not support drag'n'drop file uploads.",
            dictFallbackText: "Please use the fallback form below to upload your files like in the olden days.",
            dictFileTooBig: "File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.",
            dictInvalidFileType: "You can't upload files of this type.",
            dictResponseError: "Server responded with {{statusCode}} code.",
            dictCancelUpload: "Cancel upload",
            dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
            dictRemoveFile: "Remove file",
            dictRemoveFileConfirmation: null,
            dictMaxFilesExceeded: "You can not upload any more files.",
            accept: function (e, t) {
                return t()
            },
            init: function () {
                return o
            },
            forceFallback: !1,
            fallback: function () {
                var e, t, n, a, r, o;
                for (this.element.className = this.element.className + " dz-browser-not-supported", a = 0, r = (o = this.element.getElementsByTagName("div")).length; r > a; a++)e = o[a], /(^| )dz-message($| )/.test(e.className) && (t = e, e.className = "dz-message");
                return t || (t = i.createElement('<div class="dz-message"><span></span></div>'), this.element.appendChild(t)), (n = t.getElementsByTagName("span")[0]) && (null != n.textContent ? n.textContent = this.options.dictFallbackMessage : null != n.innerText && (n.innerText = this.options.dictFallbackMessage)), this.element.appendChild(this.getFallbackForm())
            },
            resize: function (e) {
                var t, i, n;
                return t = {
                    srcX: 0,
                    srcY: 0,
                    srcWidth: e.width,
                    srcHeight: e.height
                }, i = e.width / e.height, t.optWidth = this.options.thumbnailWidth, t.optHeight = this.options.thumbnailHeight, null == t.optWidth && null == t.optHeight ? (t.optWidth = t.srcWidth, t.optHeight = t.srcHeight) : null == t.optWidth ? t.optWidth = i * t.optHeight : null == t.optHeight && (t.optHeight = 1 / i * t.optWidth), n = t.optWidth / t.optHeight, e.height < t.optHeight || e.width < t.optWidth ? (t.trgHeight = t.srcHeight, t.trgWidth = t.srcWidth) : i > n ? (t.srcHeight = e.height, t.srcWidth = t.srcHeight * n) : (t.srcWidth = e.width, t.srcHeight = t.srcWidth / n), t.srcX = (e.width - t.srcWidth) / 2, t.srcY = (e.height - t.srcHeight) / 2, t
            },
            drop: function () {
                return this.element.classList.remove("dz-drag-hover")
            },
            dragstart: o,
            dragend: function () {
                return this.element.classList.remove("dz-drag-hover")
            },
            dragenter: function () {
                return this.element.classList.add("dz-drag-hover")
            },
            dragover: function () {
                return this.element.classList.add("dz-drag-hover")
            },
            dragleave: function () {
                return this.element.classList.remove("dz-drag-hover")
            },
            paste: o,
            reset: function () {
                return this.element.classList.remove("dz-started")
            },
            addedfile: function (e) {
                var t, n, a, r, o, s, l, c, d, u, p, m, h;
                if (this.element === this.previewsContainer && this.element.classList.add("dz-started"), this.previewsContainer) {
                    for (e.previewElement = i.createElement(this.options.previewTemplate.trim()), e.previewTemplate = e.previewElement, this.previewsContainer.appendChild(e.previewElement), r = 0, l = (u = e.previewElement.querySelectorAll("[data-dz-name]")).length; l > r; r++)t = u[r], t.textContent = this._renameFilename(e.name);
                    for (o = 0, c = (p = e.previewElement.querySelectorAll("[data-dz-size]")).length; c > o; o++)t = p[o], t.innerHTML = this.filesize(e.size);
                    for (this.options.addRemoveLinks && (e._removeLink = i.createElement('<a class="dz-remove" href="javascript:undefined;" data-dz-remove>' + this.options.dictRemoveFile + "</a>"), e.previewElement.appendChild(e._removeLink)), n = function (t) {
                        return function (n) {
                            return n.preventDefault(), n.stopPropagation(), e.status === i.UPLOADING ? i.confirm(t.options.dictCancelUploadConfirmation, function () {
                                return t.removeFile(e)
                            }) : t.options.dictRemoveFileConfirmation ? i.confirm(t.options.dictRemoveFileConfirmation, function () {
                                return t.removeFile(e)
                            }) : t.removeFile(e)
                        }
                    }(this), h = [], s = 0, d = (m = e.previewElement.querySelectorAll("[data-dz-remove]")).length; d > s; s++)a = m[s], h.push(a.addEventListener("click", n));
                    return h
                }
            },
            removedfile: function (e) {
                var t;
                return e.previewElement && null != (t = e.previewElement) && t.parentNode.removeChild(e.previewElement), this._updateMaxFilesReachedClass()
            },
            thumbnail: function (e, t) {
                var i, n, a, r;
                if (e.previewElement) {
                    for (e.previewElement.classList.remove("dz-file-preview"), n = 0, a = (r = e.previewElement.querySelectorAll("[data-dz-thumbnail]")).length; a > n; n++)i = r[n], i.alt = e.name, i.src = t;
                    return setTimeout(function () {
                        return function () {
                            return e.previewElement.classList.add("dz-image-preview")
                        }
                    }(), 1)
                }
            },
            error: function (e, t) {
                var i, n, a, r, o;
                if (e.previewElement) {
                    for (e.previewElement.classList.add("dz-error"), "String" != typeof t && t.error && (t = t.error), o = [], n = 0, a = (r = e.previewElement.querySelectorAll("[data-dz-errormessage]")).length; a > n; n++)i = r[n], o.push(i.textContent = t);
                    return o
                }
            },
            errormultiple: o,
            processing: function (e) {
                return e.previewElement && (e.previewElement.classList.add("dz-processing"), e._removeLink) ? e._removeLink.textContent = this.options.dictCancelUpload : void 0
            },
            processingmultiple: o,
            uploadprogress: function (e, t) {
                var i, n, a, r, o;
                if (e.previewElement) {
                    for (o = [], n = 0, a = (r = e.previewElement.querySelectorAll("[data-dz-uploadprogress]")).length; a > n; n++)i = r[n], o.push("PROGRESS" === i.nodeName ? i.value = t : i.style.width = t + "%");
                    return o
                }
            },
            totaluploadprogress: o,
            sending: o,
            sendingmultiple: o,
            success: function (e) {
                return e.previewElement ? e.previewElement.classList.add("dz-success") : void 0
            },
            successmultiple: o,
            canceled: function (e) {
                return this.emit("error", e, "Upload canceled.")
            },
            canceledmultiple: o,
            complete: function (e) {
                return e._removeLink && (e._removeLink.textContent = this.options.dictRemoveFile), e.previewElement ? e.previewElement.classList.add("dz-complete") : void 0
            },
            completemultiple: o,
            maxfilesexceeded: o,
            maxfilesreached: o,
            queuecomplete: o,
            addedfiles: o,
            previewTemplate: '<div class="dz-preview dz-file-preview">\n  <div class="dz-image"><img data-dz-thumbnail /></div>\n  <div class="dz-details">\n    <div class="dz-size"><span data-dz-size></span></div>\n    <div class="dz-filename"><span data-dz-name></span></div>\n  </div>\n  <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>\n  <div class="dz-error-message"><span data-dz-errormessage></span></div>\n  <div class="dz-success-mark">\n    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">\n      <title>Check</title>\n      <defs></defs>\n      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">\n        <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>\n      </g>\n    </svg>\n  </div>\n  <div class="dz-error-mark">\n    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">\n      <title>Error</title>\n      <defs></defs>\n      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">\n        <g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">\n          <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path>\n        </g>\n      </g>\n    </svg>\n  </div>\n</div>'
        }, n = function () {
            var e, t, i, n, a, r, o;
            for (n = arguments[0], r = 0, o = (i = 2 <= arguments.length ? l.call(arguments, 1) : []).length; o > r; r++) {
                t = i[r];
                for (e in t)a = t[e], n[e] = a
            }
            return n
        }, i.prototype.getAcceptedFiles = function () {
            var e, t, i, n, a;
            for (a = [], t = 0, i = (n = this.files).length; i > t; t++)(e = n[t]).accepted && a.push(e);
            return a
        }, i.prototype.getRejectedFiles = function () {
            var e, t, i, n, a;
            for (a = [], t = 0, i = (n = this.files).length; i > t; t++)(e = n[t]).accepted || a.push(e);
            return a
        }, i.prototype.getFilesWithStatus = function (e) {
            var t, i, n, a, r;
            for (r = [], i = 0, n = (a = this.files).length; n > i; i++)(t = a[i]).status === e && r.push(t);
            return r
        }, i.prototype.getQueuedFiles = function () {
            return this.getFilesWithStatus(i.QUEUED)
        }, i.prototype.getUploadingFiles = function () {
            return this.getFilesWithStatus(i.UPLOADING)
        }, i.prototype.getAddedFiles = function () {
            return this.getFilesWithStatus(i.ADDED)
        }, i.prototype.getActiveFiles = function () {
            var e, t, n, a, r;
            for (r = [], t = 0, n = (a = this.files).length; n > t; t++)((e = a[t]).status === i.UPLOADING || e.status === i.QUEUED) && r.push(e);
            return r
        }, i.prototype.init = function () {
            var e, t, n, a, r, o, s;
            for ("form" === this.element.tagName && this.element.setAttribute("enctype", "multipart/form-data"), this.element.classList.contains("dropzone") && !this.element.querySelector(".dz-message") && this.element.appendChild(i.createElement('<div class="dz-default dz-message"><span>' + this.options.dictDefaultMessage + "</span></div>")), this.clickableElements.length && (n = function (e) {
                return function () {
                    return e.hiddenFileInput && e.hiddenFileInput.parentNode.removeChild(e.hiddenFileInput), e.hiddenFileInput = document.createElement("input"), e.hiddenFileInput.setAttribute("type", "file"), (null == daDA() || daDA() > 1) && e.hiddenFileInput.setAttribute("multiple", "multiple"), e.hiddenFileInput.className = "dz-hidden-input", null != e.options.acceptedFiles && e.hiddenFileInput.setAttribute("accept", e.options.acceptedFiles), null != e.options.capture && e.hiddenFileInput.setAttribute("capture", e.options.capture), e.hiddenFileInput.style.visibility = "hidden", e.hiddenFileInput.style.position = "absolute", e.hiddenFileInput.style.top = "0", e.hiddenFileInput.style.left = "0", e.hiddenFileInput.style.height = "0", e.hiddenFileInput.style.width = "0", document.querySelector(e.options.hiddenInputContainer).appendChild(e.hiddenFileInput), e.hiddenFileInput.addEventListener("change", function () {
                        var t, i, a, r;
                        if ((i = e.hiddenFileInput.files).length)for (a = 0, r = i.length; r > a; a++)t = i[a], e.addFile(t);
                        return e.emit("addedfiles", i), n()
                    })
                }
            }(this))(), this.URL = null != (o = window.URL) ? o : window.webkitURL, a = 0, r = (s = this.events).length; r > a; a++)e = s[a], this.on(e, this.options[e]);
            return this.on("uploadprogress", function (e) {
                return function () {
                    return e.updateTotalUploadProgress()
                }
            }(this)), this.on("removedfile", function (e) {
                return function () {
                    return e.updateTotalUploadProgress()
                }
            }(this)), this.on("canceled", function (e) {
                return function (t) {
                    return e.emit("complete", t)
                }
            }(this)), this.on("complete", function (e) {
                return function () {
                    return 0 === e.getAddedFiles().length && 0 === e.getUploadingFiles().length && 0 === e.getQueuedFiles().length ? setTimeout(function () {
                        return e.emit("queuecomplete")
                    }, 0) : void 0
                }
            }(this)), t = function (e) {
                return e.stopPropagation(), e.preventDefault ? e.preventDefault() : e.returnValue = !1
            }, this.listeners = [{
                element: this.element, events: {
                    dragstart: function (e) {
                        return function (t) {
                            return e.emit("dragstart", t)
                        }
                    }(this), dragenter: function (e) {
                        return function (i) {
                            return t(i), e.emit("dragenter", i)
                        }
                    }(this), dragover: function (e) {
                        return function (i) {
                            var n;
                            try {
                                n = i.dataTransfer.effectAllowed
                            } catch (e) {
                            }
                            return i.dataTransfer.dropEffect = "move" === n || "linkMove" === n ? "move" : "copy", t(i), e.emit("dragover", i)
                        }
                    }(this), dragleave: function (e) {
                        return function (t) {
                            return e.emit("dragleave", t)
                        }
                    }(this), drop: function (e) {
                        return function (i) {
                            return t(i), e.drop(i)
                        }
                    }(this), dragend: function (e) {
                        return function (t) {
                            return e.emit("dragend", t)
                        }
                    }(this)
                }
            }], this.clickableElements.forEach(function (e) {
                return function (t) {
                    return e.listeners.push({
                        element: t, events: {
                            click: function (n) {
                                return (t !== e.element || n.target === e.element || i.elementInside(n.target, e.element.querySelector(".dz-message"))) && e.hiddenFileInput.click(), !0
                            }
                        }
                    })
                }
            }(this)), this.enable(), this.options.init.call(this)
        }, i.prototype.destroy = function () {
            var e;
            return this.disable(), this.removeAllFiles(!0), (null != (e = this.hiddenFileInput) ? e.parentNode : void 0) && (this.hiddenFileInput.parentNode.removeChild(this.hiddenFileInput), this.hiddenFileInput = null), delete this.element.dropzone, i.instances.splice(i.instances.indexOf(this), 1)
        }, i.prototype.updateTotalUploadProgress = function () {
            var e, t, i, n, a, r, o;
            if (i = 0, t = 0, this.getActiveFiles().length) {
                for (a = 0, r = (o = this.getActiveFiles()).length; r > a; a++)e = o[a], i += e.upload.bytesSent, t += e.upload.total;
                n = 100 * i / t
            } else n = 100;
            return this.emit("totaluploadprogress", n, t, i)
        }, i.prototype._getParamName = function (e) {
            return "function" == typeof this.options.paramName ? this.options.paramName(e) : this.options.paramName + (this.options.uploadMultiple ? "[" + e + "]" : "")
        }, i.prototype._renameFilename = function (e) {
            return "function" != typeof this.options.renameFilename ? e : this.options.renameFilename(e)
        }, i.prototype.getFallbackForm = function () {
            var e, t, n, a;
            return (e = this.getExistingFallback()) ? e : (n = '<div class="dz-fallback">', this.options.dictFallbackText && (n += "<p>" + this.options.dictFallbackText + "</p>"), n += '<input type="file" name="' + this._getParamName(0) + '" ' + (this.options.uploadMultiple ? 'multiple="multiple"' : void 0) + ' /><input type="submit" value="Upload!"></div>', t = i.createElement(n), "FORM" !== this.element.tagName ? (a = i.createElement('<form action="' + this.options.url + '" enctype="multipart/form-data" method="' + this.options.method + '"></form>')).appendChild(t) : (this.element.setAttribute("enctype", "multipart/form-data"), this.element.setAttribute("method", this.options.method)), null != a ? a : t)
        }, i.prototype.getExistingFallback = function () {
            var e, t, i, n, a, r;
            for (t = function (e) {
                var t, i, n;
                for (i = 0, n = e.length; n > i; i++)if (t = e[i], /(^| )fallback($| )/.test(t.className))return t
            }, n = 0, a = (r = ["div", "form"]).length; a > n; n++)if (i = r[n], e = t(this.element.getElementsByTagName(i)))return e
        }, i.prototype.setupEventListeners = function () {
            var e, t, i, n, a, r, o;
            for (o = [], n = 0, a = (r = this.listeners).length; a > n; n++)e = r[n], o.push(function () {
                var n, a;
                n = e.events, a = [];
                for (t in n)i = n[t], a.push(e.element.addEventListener(t, i, !1));
                return a
            }());
            return o
        }, i.prototype.removeEventListeners = function () {
            var e, t, i, n, a, r, o;
            for (o = [], n = 0, a = (r = this.listeners).length; a > n; n++)e = r[n], o.push(function () {
                var n, a;
                n = e.events, a = [];
                for (t in n)i = n[t], a.push(e.element.removeEventListener(t, i, !1));
                return a
            }());
            return o
        }, i.prototype.disable = function () {
            var e, t, i, n, a;
            for (this.clickableElements.forEach(function (e) {
                return e.classList.remove("dz-clickable")
            }), this.removeEventListeners(), a = [], t = 0, i = (n = this.files).length; i > t; t++)e = n[t], a.push(this.cancelUpload(e));
            return a
        }, i.prototype.enable = function () {
            return this.clickableElements.forEach(function (e) {
                return e.classList.add("dz-clickable")
            }), this.setupEventListeners()
        }, i.prototype.filesize = function (e) {
            var t, i, n, a, r, o, s, l;
            if (n = 0, a = "b", e > 0) {
                for (i = s = 0, l = (o = ["TB", "GB", "MB", "KB", "b"]).length; l > s; i = ++s)if (r = o[i], t = Math.pow(this.options.filesizeBase, 4 - i) / 10, e >= t) {
                    n = e / Math.pow(this.options.filesizeBase, 4 - i), a = r;
                    break
                }
                n = Math.round(10 * n) / 10
            }
            return "<strong>" + n + "</strong> " + a
        }, i.prototype._updateMaxFilesReachedClass = function () {
            return null != daDA() && this.getAcceptedFiles().length >= daDA() ? (this.getAcceptedFiles().length === daDA() && this.emit("maxfilesreached", this.files), this.element.classList.add("dz-max-files-reached")) : this.element.classList.remove("dz-max-files-reached")
        }, i.prototype.drop = function (e) {
            var t, i;
            e.dataTransfer && (this.emit("drop", e), t = e.dataTransfer.files, this.emit("addedfiles", t), t.length && (i = e.dataTransfer.items, i && i.length && null != i[0].webkitGetAsEntry ? this._addFilesFromItems(i) : this.handleFiles(t)))
        }, i.prototype.paste = function (e) {
            var t, i;
            return null != (null != e && null != (i = e.clipboardData) ? i.items : void 0) ? (this.emit("paste", e), t = e.clipboardData.items, t.length ? this._addFilesFromItems(t) : void 0) : void 0
        }, i.prototype.handleFiles = function (e) {
            var t, i, n, a;
            for (a = [], i = 0, n = e.length; n > i; i++)t = e[i], a.push(this.addFile(t));
            return a
        }, i.prototype._addFilesFromItems = function (e) {
            var t, i, n, a, r;
            for (r = [], n = 0, a = e.length; a > n; n++)i = e[n], r.push(null != i.webkitGetAsEntry && (t = i.webkitGetAsEntry()) ? t.isFile ? this.addFile(i.getAsFile()) : t.isDirectory ? this._addFilesFromDirectory(t, t.name) : void 0 : null == i.getAsFile || null != i.kind && "file" !== i.kind ? void 0 : this.addFile(i.getAsFile()));
            return r
        }, i.prototype._addFilesFromDirectory = function (e, t) {
            var i, n, a;
            return i = e.createReader(), n = function (e) {
                return "undefined" != typeof console && null !== console && "function" == typeof console.log ? console.log(e) : void 0
            }, (a = function (e) {
                return function () {
                    return i.readEntries(function (i) {
                        var n, r, o;
                        if (i.length > 0) {
                            for (r = 0, o = i.length; o > r; r++)n = i[r], n.isFile ? n.file(function (i) {
                                return e.options.ignoreHiddenFiles && "." === i.name.substring(0, 1) ? void 0 : (i.fullPath = t + "/" + i.name, e.addFile(i))
                            }) : n.isDirectory && e._addFilesFromDirectory(n, t + "/" + n.name);
                            a()
                        }
                        return null
                    }, n)
                }
            }(this))()
        }, i.prototype.accept = function (e, t) {
            return e.size > 1024 * this.options.maxFilesize * 1024 ? t(this.options.dictFileTooBig.replace("{{filesize}}", Math.round(e.size / 1024 / 10.24) / 100).replace("{{maxFilesize}}", this.options.maxFilesize)) : i.isValidFile(e, this.options.acceptedFiles) ? null != daDA() && this.getAcceptedFiles().length >= daDA() ? (t(this.options.dictMaxFilesExceeded.replace("{{maxFiles}}", daDA())), this.emit("maxfilesexceeded", e)) : this.options.accept.call(this, e, t) : t(this.options.dictInvalidFileType)
        }, i.prototype.addFile = function (e) {
            return e.upload = {
                progress: 0,
                total: e.size,
                bytesSent: 0
            }, this.files.push(e), e.status = i.ADDED, this.emit("addedfile", e), this._enqueueThumbnail(e), this.accept(e, function (t) {
                return function (i) {
                    return i ? (e.accepted = !1, t._errorProcessing([e], i)) : (e.accepted = !0, t.options.autoQueue && t.enqueueFile(e)), t._updateMaxFilesReachedClass()
                }
            }(this))
        }, i.prototype.enqueueFiles = function (e) {
            var t, i, n;
            for (i = 0, n = e.length; n > i; i++)t = e[i], this.enqueueFile(t);
            return null
        }, i.prototype.enqueueFile = function (e) {
            if (e.status !== i.ADDED || !0 !== e.accepted)throw new Error("This file can't be queued because it has already been processed or was rejected.");
            return e.status = i.QUEUED, this.options.autoProcessQueue ? setTimeout(function (e) {
                return function () {
                    return e.processQueue()
                }
            }(this), 0) : void 0
        }, i.prototype._thumbnailQueue = [], i.prototype._processingThumbnail = !1, i.prototype._enqueueThumbnail = function (e) {
            return this.options.createImageThumbnails && e.type.match(/image.*/) && e.size <= 1024 * this.options.maxThumbnailFilesize * 1024 ? (this._thumbnailQueue.push(e), setTimeout(function (e) {
                return function () {
                    return e._processThumbnailQueue()
                }
            }(this), 0)) : void 0
        }, i.prototype._processThumbnailQueue = function () {
            return this._processingThumbnail || 0 === this._thumbnailQueue.length ? void 0 : (this._processingThumbnail = !0, this.createThumbnail(this._thumbnailQueue.shift(), function (e) {
                return function () {
                    return e._processingThumbnail = !1, e._processThumbnailQueue()
                }
            }(this)))
        }, i.prototype.removeFile = function (e) {
            return e.status === i.UPLOADING && this.cancelUpload(e), this.files = s(this.files, e), this.emit("removedfile", e), 0 === this.files.length ? this.emit("reset") : void 0
        }, i.prototype.removeAllFiles = function (e) {
            var t, n, a, r;
            for (null == e && (e = !1), n = 0, a = (r = this.files.slice()).length; a > n; n++)((t = r[n]).status !== i.UPLOADING || e) && this.removeFile(t);
            return null
        }, i.prototype.createThumbnail = function (e, t) {
            var i;
            return i = new FileReader, i.onload = function (n) {
                return function () {
                    return "image/svg+xml" === e.type ? (n.emit("thumbnail", e, i.result), void(null != t && t())) : n.createThumbnailFromUrl(e, i.result, t)
                }
            }(this), i.readAsDataURL(e)
        }, i.prototype.createThumbnailFromUrl = function (e, t, i, n) {
            var a;
            return a = document.createElement("img"), n && (a.crossOrigin = n), a.onload = function (t) {
                return function () {
                    var n, o, s, l, c, d, u, p;
                    return e.width = a.width, e.height = a.height, null == (s = t.options.resize.call(t, e)).trgWidth && (s.trgWidth = s.optWidth), null == s.trgHeight && (s.trgHeight = s.optHeight), n = document.createElement("canvas"), o = n.getContext("2d"), n.width = s.trgWidth, n.height = s.trgHeight, r(o, a, null != (c = s.srcX) ? c : 0, null != (d = s.srcY) ? d : 0, s.srcWidth, s.srcHeight, null != (u = s.trgX) ? u : 0, null != (p = s.trgY) ? p : 0, s.trgWidth, s.trgHeight), l = n.toDataURL("image/png"), t.emit("thumbnail", e, l), null != i ? i() : void 0
                }
            }(this), null != i && (a.onerror = i), a.src = t
        }, i.prototype.processQueue = function () {
            var e, t, i, n;
            if (t = this.options.parallelUploads, i = this.getUploadingFiles().length, e = i, !(i >= t) && (n = this.getQueuedFiles()).length > 0) {
                if (this.options.uploadMultiple)return this.processFiles(n.slice(0, t - i));
                for (; t > e;) {
                    if (!n.length)return;
                    this.processFile(n.shift()), e++
                }
            }
        }, i.prototype.processFile = function (e) {
            return this.processFiles([e])
        }, i.prototype.processFiles = function (e) {
            var t, n, a;
            for (n = 0, a = e.length; a > n; n++)t = e[n], t.processing = !0, t.status = i.UPLOADING, this.emit("processing", t);
            return this.options.uploadMultiple && this.emit("processingmultiple", e), this.uploadFiles(e)
        }, i.prototype._getFilesWithXhr = function (e) {
            var t;
            return function () {
                var i, n, a, r;
                for (r = [], i = 0, n = (a = this.files).length; n > i; i++)(t = a[i]).xhr === e && r.push(t);
                return r
            }.call(this)
        }, i.prototype.cancelUpload = function (e) {
            var t, n, a, r, o, s, l;
            if (e.status === i.UPLOADING) {
                for (a = 0, o = (n = this._getFilesWithXhr(e.xhr)).length; o > a; a++)t = n[a], t.status = i.CANCELED;
                for (e.xhr.abort(), r = 0, s = n.length; s > r; r++)t = n[r], this.emit("canceled", t);
                this.options.uploadMultiple && this.emit("canceledmultiple", n)
            } else((l = e.status) === i.ADDED || l === i.QUEUED) && (e.status = i.CANCELED, this.emit("canceled", e), this.options.uploadMultiple && this.emit("canceledmultiple", [e]));
            return this.options.autoProcessQueue ? this.processQueue() : void 0
        }, a = function () {
            var e, t;
            return t = arguments[0], e = 2 <= arguments.length ? l.call(arguments, 1) : [], "function" == typeof t ? t.apply(this, e) : t
        }, i.prototype.uploadFile = function (e) {
            return this.uploadFiles([e])
        }, i.prototype.uploadFiles = function (e) {
            var t, r, o, s, l, c, d, u, p, m, h, f, g, v, y, b, _, C, w, x, k, T, E, F, I, j, L, A, z, D, P, S, U;
            for (C = new XMLHttpRequest, w = 0, E = e.length; E > w; w++)t = e[w], t.xhr = C;
            f = a(this.options.method, e), b = a(this.options.url, e), C.open(f, b, !0), C.withCredentials = !!this.options.withCredentials, v = null, o = function (i) {
                return function () {
                    var n, a, r;
                    for (r = [], n = 0, a = e.length; a > n; n++)t = e[n], r.push(i._errorProcessing(e, v || i.options.dictResponseError.replace("{{statusCode}}", C.status), C));
                    return r
                }
            }(this), y = function (i) {
                return function (n) {
                    var a, r, o, s, l, c, d, u, p;
                    if (null != n)for (r = 100 * n.loaded / n.total, o = 0, c = e.length; c > o; o++)t = e[o], t.upload = {
                        progress: r,
                        total: n.total,
                        bytesSent: n.loaded
                    }; else {
                        for (a = !0, r = 100, s = 0, d = e.length; d > s; s++)(100 !== (t = e[s]).upload.progress || t.upload.bytesSent !== t.upload.total) && (a = !1), t.upload.progress = r, t.upload.bytesSent = t.upload.total;
                        if (a)return
                    }
                    for (p = [], l = 0, u = e.length; u > l; l++)t = e[l], p.push(i.emit("uploadprogress", t, r, t.upload.bytesSent));
                    return p
                }
            }(this), C.onload = function (t) {
                return function (n) {
                    var a;
                    if (e[0].status !== i.CANCELED && 4 === C.readyState) {
                        if (v = C.responseText, C.getResponseHeader("content-type") && ~C.getResponseHeader("content-type").indexOf("application/json"))try {
                            v = JSON.parse(v)
                        } catch (e) {
                            n = e, v = "Invalid JSON response from server."
                        }
                        return y(), 200 <= (a = C.status) && 300 > a ? t._finished(e, v, n) : o()
                    }
                }
            }(this), C.onerror = function () {
                return function () {
                    return e[0].status !== i.CANCELED ? o() : void 0
                }
            }(), (null != (A = C.upload) ? A : C).onprogress = y, c = {
                Accept: "application/json",
                "Cache-Control": "no-cache",
                "X-Requested-With": "XMLHttpRequest"
            }, this.options.headers && n(c, this.options.headers);
            for (s in c)(l = c[s]) && C.setRequestHeader(s, l);
            if (r = new FormData, this.options.params) {
                z = this.options.params;
                for (h in z)_ = z[h], r.append(h, _)
            }
            for (x = 0, F = e.length; F > x; x++)t = e[x], this.emit("sending", t, C, r);
            if (this.options.uploadMultiple && this.emit("sendingmultiple", e, C, r), "FORM" === this.element.tagName)for (D = this.element.querySelectorAll("input, textarea, select, button"), k = 0, I = D.length; I > k; k++)if (u = D[k], p = u.getAttribute("name"), m = u.getAttribute("type"), "SELECT" === u.tagName && u.hasAttribute("multiple"))for (P = u.options, T = 0, j = P.length; j > T; T++)(g = P[T]).selected && r.append(p, g.value); else(!m || "checkbox" !== (S = m.toLowerCase()) && "radio" !== S || u.checked) && r.append(p, u.value);
            for (d = L = 0, U = e.length - 1; U >= 0 ? U >= L : L >= U; d = U >= 0 ? ++L : --L)r.append(this._getParamName(d), e[d], this._renameFilename(e[d].name));
            return this.submitRequest(C, r, e)
        }, i.prototype.submitRequest = function (e, t) {
            return e.send(t)
        }, i.prototype._finished = function (e, t, n) {
            var a, r, o;
            for (r = 0, o = e.length; o > r; r++)a = e[r], a.status = i.SUCCESS, this.emit("success", a, t, n), this.emit("complete", a);
            return this.options.uploadMultiple && (this.emit("successmultiple", e, t, n), this.emit("completemultiple", e)), this.options.autoProcessQueue ? this.processQueue() : void 0
        }, i.prototype._errorProcessing = function (e, t, n) {
            var a, r, o;
            for (r = 0, o = e.length; o > r; r++)a = e[r], a.status = i.ERROR, this.emit("error", a, t, n), this.emit("complete", a);
            return this.options.uploadMultiple && (this.emit("errormultiple", e, t, n), this.emit("completemultiple", e)), this.options.autoProcessQueue ? this.processQueue() : void 0
        }, i
    }(t)).version = "4.3.0", e.options = {}, e.optionsForElement = function (t) {
        return t.getAttribute("id") ? e.options[i(t.getAttribute("id"))] : void 0
    }, e.instances = [], e.forElement = function (e) {
        if ("string" == typeof e && (e = document.querySelector(e)), null == (null != e ? e.dropzone : void 0))throw new Error("No Dropzone found for given element. This is probably because you're trying to access it before Dropzone had the time to initialize. Use the `init` option to setup any additional observers on your Dropzone.");
        return e.dropzone
    }, e.autoDiscover = !0, e.discover = function () {
        var t, i, n, a, r, o;
        for (document.querySelectorAll ? n = document.querySelectorAll(".dropzone") : (n = [], (t = function (e) {
            var t, i, a, r;
            for (r = [], i = 0, a = e.length; a > i; i++)t = e[i], r.push(/(^| )dropzone($| )/.test(t.className) ? n.push(t) : void 0);
            return r
        })(document.getElementsByTagName("div")), t(document.getElementsByTagName("form"))), o = [], a = 0, r = n.length; r > a; a++)i = n[a], o.push(!1 !== e.optionsForElement(i) ? new e(i) : void 0);
        return o
    }, e.blacklistedBrowsers = [/opera.*Macintosh.*version\/12/i], e.isBrowserSupported = function () {
        var t, i, n, a;
        if (t = !0, window.File && window.FileReader && window.FileList && window.Blob && window.FormData && document.querySelector)if ("classList"in document.createElement("a"))for (a = e.blacklistedBrowsers, i = 0, n = a.length; n > i; i++)a[i].test(navigator.userAgent) && (t = !1); else t = !1; else t = !1;
        return t
    }, s = function (e, t) {
        var i, n, a, r;
        for (r = [], n = 0, a = e.length; a > n; n++)(i = e[n]) !== t && r.push(i);
        return r
    }, i = function (e) {
        return e.replace(/[\-_](\w)/g, function (e) {
            return e.charAt(1).toUpperCase()
        })
    }, e.createElement = function (e) {
        var t;
        return t = document.createElement("div"), t.innerHTML = e, t.childNodes[0]
    }, e.elementInside = function (e, t) {
        if (e === t)return !0;
        for (; e = e.parentNode;)if (e === t)return !0;
        return !1
    }, e.getElement = function (e, t) {
        var i;
        if ("string" == typeof e ? i = document.querySelector(e) : null != e.nodeType && (i = e), null == i)throw new Error("Invalid `" + t + "` option provided. Please provide a CSS selector or a plain HTML element.");
        return i
    }, e.getElements = function (e, t) {
        var i, n, a, r, o, s, l;
        if (e instanceof Array) {
            n = [];
            try {
                for (a = 0, o = e.length; o > a; a++)i = e[a], n.push(this.getElement(i, t))
            } catch (e) {
                e, n = null
            }
        } else if ("string" == typeof e)for (n = [], l = document.querySelectorAll(e), r = 0, s = l.length; s > r; r++)i = l[r], n.push(i); else null != e.nodeType && (n = [e]);
        if (null == n || !n.length)throw new Error("Invalid `" + t + "` option provided. Please provide a CSS selector, a plain HTML element or a list of those.");
        return n
    }, e.confirm = function (e, t, i) {
        return window.confirm(e) ? t() : null != i ? i() : void 0
    }, e.isValidFile = function (e, t) {
        var i, n, a, r, o;
        if (!t)return !0;
        for (t = t.split(","), i = (n = e.type).replace(/\/.*$/, ""), r = 0, o = t.length; o > r; r++)if (a = t[r], "." === (a = a.trim()).charAt(0)) {
            if (-1 !== e.name.toLowerCase().indexOf(a.toLowerCase(), e.name.length - a.length))return !0
        } else if (/\/\*$/.test(a)) {
            if (i === a.replace(/\/.*$/, ""))return !0
        } else if (n === a)return !0;
        return !1
    }, "undefined" != typeof jQuery && null !== jQuery && (jQuery.fn.dropzone = function (t) {
        return this.each(function () {
            return new e(this, t)
        })
    }), "undefined" != typeof module && null !== module ? module.exports = e : window.Dropzone = e, e.ADDED = "added", e.QUEUED = "queued", e.ACCEPTED = e.QUEUED, e.UPLOADING = "uploading", e.PROCESSING = e.UPLOADING, e.CANCELED = "canceled", e.ERROR = "error", e.SUCCESS = "success", a = function (e) {
        var t, i, n, a, r, o, s, l, c;
        for (e.naturalWidth, o = e.naturalHeight, (i = document.createElement("canvas")).width = 1, i.height = o, (n = i.getContext("2d")).drawImage(e, 0, 0), a = n.getImageData(0, 0, 1, o).data, c = 0, r = o, s = o; s > c;)t = a[4 * (s - 1) + 3], 0 === t ? r = s : c = s, s = r + c >> 1;
        return l = s / o, 0 === l ? 1 : l
    }, r = function (e, t, i, n, r, o, s, l, c, d) {
        var u;
        return u = a(t), e.drawImage(t, i, n, r, o, s, l, c, d / u)
    }, n = function (e, t) {
        var i, n, a, r, o, s, l, c, d;
        if (a = !1, d = !0, n = e.document, c = n.documentElement, i = n.addEventListener ? "addEventListener" : "attachEvent", l = n.addEventListener ? "removeEventListener" : "detachEvent", s = n.addEventListener ? "" : "on", r = function (i) {
                return "readystatechange" !== i.type || "complete" === n.readyState ? (("load" === i.type ? e : n)[l](s + i.type, r, !1), !a && (a = !0) ? t.call(e, i.type || i) : void 0) : void 0
            }, o = function () {
                try {
                    c.doScroll("left")
                } catch (e) {
                    return e, void setTimeout(o, 50)
                }
                return r("poll")
            }, "complete" !== n.readyState) {
            if (n.createEventObject && c.doScroll) {
                try {
                    d = !e.frameElement
                } catch (e) {
                }
                d && o()
            }
            return n[i](s + "DOMContentLoaded", r, !1), n[i](s + "readystatechange", r, !1), e[i](s + "load", r, !1)
        }
    }, e._autoDiscoverFunction = function () {
        return e.autoDiscover ? e.discover() : void 0
    }, n(window, e._autoDiscoverFunction)
}).call(this), function (e) {
    "function" == typeof define && define.amd ? define(["jquery"], e) : "object" == typeof module && module.exports ? module.exports = function (t, i) {
        return void 0 === i && (i = "undefined" != typeof window ? require("jquery") : require("jquery")(t)), e(i), i
    } : e(jQuery)
}(function (e) {
    var t = {
        $el: null,
        commentsById: {},
        usersById: {},
        dataFetched: !1,
        currentSortKey: "",
        options: {},
        events: {
            click: "closeDropdowns",
            "keydown [contenteditable]": "saveOnKeydown",
            "focus [contenteditable]": "saveEditableContent",
            "keyup [contenteditable]": "checkEditableContentForChange",
            "paste [contenteditable]": "checkEditableContentForChange",
            "input [contenteditable]": "checkEditableContentForChange",
            "blur [contenteditable]": "checkEditableContentForChange",
            "click .navigation li[data-sort-key]": "navigationElementClicked",
            "click .navigation li.title": "toggleNavigationDropdown",
            "click .commenting-field.main .textarea": "showMainCommentingField",
            "click .commenting-field.main .close": "hideMainCommentingField",
            "click .commenting-field .textarea": "increaseTextareaHeight",
            "change .commenting-field .textarea": "increaseTextareaHeight textareaContentChanged",
            "click .commenting-field:not(.main) .close": "removeCommentingField",
            "click .commenting-field .send.enabled": "postComment",
            "click .commenting-field .update.enabled": "putComment",
            "click .commenting-field .delete.enabled": "deleteComment",
            'change .commenting-field .upload.enabled input[type="file"]': "fileInputChanged",
            "click li.comment button.upvote": "upvoteComment",
            "click li.comment button.delete.enabled": "deleteComment",
            "click li.comment .hashtag": "hashtagClicked",
            "click li.comment .ping": "pingClicked",
            "click li.comment ul.child-comments .toggle-all": "toggleReplies",
            "click li.comment button.reply": "replyButtonClicked",
            "click li.comment button.edit": "editButtonClicked",
            dragenter: "showDroppableOverlay",
            "dragenter .droppable-overlay": "handleDragEnter",
            "dragleave .droppable-overlay": "handleDragLeaveForOverlay",
            "dragenter .droppable-overlay .droppable": "handleDragEnter",
            "dragleave .droppable-overlay .droppable": "handleDragLeaveForDroppable",
            "dragover .droppable-overlay": "handleDragOverForOverlay",
            "drop .droppable-overlay": "handleDrop",
            "click .dropdown.autocomplete": "stopPropagation",
            "mousedown .dropdown.autocomplete": "stopPropagation",
            "touchstart .dropdown.autocomplete": "stopPropagation"
        },
        getDefaultOptions: function () {
            return {
                profilePictureURL: "",
                currentUserIsAdmin: !1,
                currentUserId: null,
                spinnerIconURL: "",
                upvoteIconURL: "",
                replyIconURL: "",
                uploadIconURL: "",
                attachmentIconURL: "",
                fileIconURL: "",
                noCommentsIconURL: "",
                textareaPlaceholderText: "Add a comment",
                newestText: "Newest",
                oldestText: "Oldest",
                popularText: "Popular",
                attachmentsText: "Attachments",
                sendText: "Send",
                replyText: "Reply",
                editText: "Edit",
                editedText: "Edited",
                youText: "You",
                saveText: "Save",
                deleteText: "Delete",
                viewAllRepliesText: "View all __replyCount__ replies",
                hideRepliesText: "Hide replies",
                noCommentsText: "No comments",
                noAttachmentsText: "No attachments",
                attachmentDropText: "Drop files here",
                textFormatter: function (e) {
                    return e
                },
                enableReplying: !0,
                enableEditing: !0,
                enableUpvoting: !0,
                enableDeleting: !0,
                enableAttachments: !1,
                enableHashtags: !1,
                enablePinging: !1,
                enableDeletingCommentWithReplies: !1,
                enableNavigation: !0,
                postCommentOnEnter: !1,
                forceResponsive: !1,
                readOnly: !1,
                defaultNavigationSortKey: "newest",
                highlightColor: "#2793e6",
                deleteButtonColor: "#C9302C",
                roundProfilePictures: !1,
                textareaRows: 2,
                textareaRowsOnFocus: 2,
                textareaMaxRows: 5,
                maxRepliesVisible: 2,
                fieldMappings: {
                    id: "id",
                    parent: "parent",
                    created: "created",
                    modified: "modified",
                    content: "content",
                    file: "file",
                    fileURL: "file_url",
                    fileMimeType: "file_mime_type",
                    pings: "pings",
                    creator: "creator",
                    fullname: "fullname",
                    profileURL: "profile_url",
                    profilePictureURL: "profile_picture_url",
                    createdByAdmin: "created_by_admin",
                    createdByCurrentUser: "created_by_current_user",
                    upvoteCount: "upvote_count",
                    userHasUpvoted: "user_has_upvoted"
                },
                getUsers: function (e) {
                    e([])
                },
                getComments: function (e) {
                    e([])
                },
                postComment: function (e, t) {
                    t(e)
                },
                putComment: function (e, t) {
                    t(e)
                },
                deleteComment: function (e, t) {
                    t()
                },
                upvoteComment: function (e, t) {
                    t(e)
                },
                hashtagClicked: function () {
                },
                pingClicked: function () {
                },
                uploadAttachments: function (e, t) {
                    t(e)
                },
                refresh: function () {
                },
                timeFormatter: function (e) {
                    return new Date(e).toLocaleDateString()
                }
            }
        },
        init: function (t, i) {
            this.$el = e(i), this.$el.addClass("jquery-comments"), this.undelegateEvents(), this.delegateEvents(), function (e) {
                (jQuery.browser = jQuery.browser || {}).mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(e) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(e.substr(0, 4))
            }(navigator.userAgent || navigator.vendor || window.opera), e.browser.mobile && this.$el.addClass("mobile"), this.options = e.extend(!0, {}, this.getDefaultOptions(), t), this.options.readOnly && this.$el.addClass("read-only"), this.currentSortKey = this.options.defaultNavigationSortKey, this.createCssDeclarations(), this.fetchDataAndRender()
        },
        delegateEvents: function () {
            this.bindEvents(!1)
        },
        undelegateEvents: function () {
            this.bindEvents(!0)
        },
        bindEvents: function (t) {
            var i = t ? "off" : "on";
            for (var n in this.events) {
                var a = n.split(" ")[0], r = n.split(" ").slice(1).join(" "), o = this.events[n].split(" ");
                for (var s in o)if (o.hasOwnProperty(s)) {
                    var l = this[o[s]];
                    l = e.proxy(l, this), "" == r ? this.$el[i](a, l) : this.$el[i](a, r, l)
                }
            }
        },
        fetchDataAndRender: function () {
            var t = this;
            this.commentsById = {}, this.usersById = {}, this.$el.empty(), this.createHTML();
            var i = this.after(this.options.enablePinging ? 2 : 1, function () {
                t.dataFetched = !0, t.render()
            }), n = function (n) {
                var a = n.map(function (e) {
                    return t.createCommentModel(e)
                });
                t.sortComments(a, "oldest"), e(a).each(function (e, i) {
                    t.addCommentToDataModel(i)
                }), i()
            };
            if (this.options.getComments(n, i), this.options.enablePinging) {
                var a = function (n) {
                    e(n).each(function (e, i) {
                        t.usersById[i.id] = i
                    }), i()
                };
                this.options.getUsers(a, i)
            }
        },
        fetchNext: function () {
            var t = this, i = this.createSpinner();
            this.$el.find("ul#comment-list").append(i);
            var n = function (n) {
                e(n).each(function (e, i) {
                    t.createComment(i)
                }), i.remove()
            }, a = function () {
                i.remove()
            };
            this.options.getComments(n, a)
        },
        createCommentModel: function (e) {
            var t = this.applyInternalMappings(e);
            return t.childs = [], t
        },
        addCommentToDataModel: function (e) {
            e.id in this.commentsById || (this.commentsById[e.id] = e, !e.parent) || this.getOutermostParent(e.parent).childs.push(e.id)
        },
        updateCommentModel: function (t) {
            e.extend(this.commentsById[t.id], t)
        },
        render: function () {
            this.dataFetched && (this.showActiveContainer(), this.createComments(), this.options.enableAttachments && this.createAttachments(), this.$el.find("> .spinner").remove(), this.options.refresh())
        },
        showActiveContainer: function () {
            var e = this.$el.find(".navigation li[data-container-name].active").data("container-name"), t = this.$el.find('[data-container="' + e + '"]');
            t.siblings("[data-container]").hide(), t.show()
        },
        createComments: function () {
            var t = this;
            this.$el.find("#comment-list").remove();
            var i = e("<ul/>", {id: "comment-list", class: "main"}), n = [], a = [];
            e(this.getComments()).each(function (e, t) {
                null == t.parent ? n.push(t) : a.push(t)
            }), this.sortComments(n, this.currentSortKey), n.reverse(), e(n).each(function (e, n) {
                t.addComment(n, i)
            }), this.sortComments(a, "oldest"), e(a).each(function (e, n) {
                t.addComment(n, i)
            }), this.$el.find('[data-container="comments"]').prepend(i)
        },
        createAttachments: function () {
            var t = this;
            this.$el.find("#attachment-list").remove();
            var i = e("<ul/>", {id: "attachment-list", class: "main"}), n = this.getAttachments();
            this.sortComments(n, "newest"), n.reverse(), e(n).each(function (e, n) {
                t.addAttachment(n, i)
            }), this.$el.find('[data-container="attachments"]').prepend(i)
        },
        addComment: function (e, t) {
            t = t || this.$el.find("#comment-list");
            var i = this.createCommentElement(e);
            if (e.parent) {
                var n = t.find('.comment[data-id="' + e.parent + '"]');
                this.reRenderCommentActionBar(e.parent);
                var a = n.parents(".comment").last();
                0 == a.length && (a = n);
                var r = a.find(".child-comments"), o = r.find(".commenting-field");
                o.length ? o.before(i) : r.append(i), this.updateToggleAllButton(a)
            } else t.prepend(i)
        },
        addAttachment: function (e, t) {
            t = t || this.$el.find("#attachment-list");
            var i = this.createCommentElement(e);
            t.prepend(i)
        },
        removeComment: function (t) {
            var i = this, n = this.commentsById[t], a = this.getChildComments(n.id);
            if (e(a).each(function (e, t) {
                    i.removeComment(t.id)
                }), n.parent) {
                var r = this.getOutermostParent(n.parent), o = r.childs.indexOf(n.id);
                r.childs.splice(o, 1)
            }
            delete this.commentsById[t];
            var s = this.$el.find('li.comment[data-id="' + t + '"]'), l = s.parents("li.comment").last();
            s.remove(), this.updateToggleAllButton(l)
        },
        uploadAttachments: function (t, i) {
            var n = this;
            i || (i = this.$el.find(".commenting-field.main"));
            var a = !i.hasClass("main"), r = t.length;
            if (r) {
                var o = i.find(".upload"), s = i.find(".textarea");
                o.removeClass("enabled");
                var l = this.createSpinner(), c = this.createSpinner();
                this.$el.find("ul#attachment-list").prepend(l), a ? i.before(c) : this.$el.find("ul#comment-list").prepend(c);
                var d = function (t) {
                    e(t).each(function (e, t) {
                        var i = n.createCommentModel(t);
                        n.addCommentToDataModel(i), n.addComment(i), n.addAttachment(i)
                    }), t.length == r && 0 == n.getTextareaContent(s).length && i.find(".close").trigger("click"), o.addClass("enabled"), c.remove(), l.remove()
                }, u = function () {
                    o.addClass("enabled"), c.remove(), l.remove()
                }, p = [];
                e(t).each(function (e, t) {
                    var i = n.createCommentJSON(s);
                    i.id += "-" + e, i.content = "", i.file = t, i.fileURL = "C:/fakepath/" + t.name, i.fileMimeType = t.type, i = n.applyExternalMappings(i), p.push(i)
                }), n.options.uploadAttachments(p, d, u)
            }
            o.find("input").val("")
        },
        updateToggleAllButton: function (t) {
            var i = t.find(".child-comments"), n = i.find(".comment"), a = i.find("li.toggle-all");
            n.removeClass("hidden-reply");
            var r = n.slice(0, -this.options.maxRepliesVisible);
            if (r.addClass("hidden-reply"), a.find("span.text").text() == this.options.textFormatter(this.options.hideRepliesText) && r.addClass("visible"), n.length > this.options.maxRepliesVisible) {
                if (!a.length) {
                    a = e("<li/>", {class: "toggle-all highlight-font-bold"});
                    var o = e("<span/>", {class: "text"}), s = e("<span/>", {class: "caret"});
                    a.append(o).append(s), i.prepend(a)
                }
                this.setToggleAllButtonText(a, !1)
            } else a.remove()
        },
        sortComments: function (e, t) {
            var i = this;
            "popularity" == t ? e.sort(function (e, t) {
                var n = e.childs.length, a = t.childs.length;
                if (i.options.enableUpvoting && (n += e.upvoteCount, a += t.upvoteCount), a != n)return a - n;
                var r = new Date(e.created).getTime();
                return new Date(t.created).getTime() - r
            }) : e.sort(function (e, i) {
                var n = new Date(e.created).getTime(), a = new Date(i.created).getTime();
                return "oldest" == t ? n - a : a - n
            })
        },
        sortUsers: function (e) {
            e.sort(function (e, t) {
                var i = e.fullname.toLowerCase().trim(), n = t.fullname.toLowerCase().trim();
                return n > i ? -1 : i > n ? 1 : 0
            })
        },
        sortAndReArrangeComments: function (t) {
            var i = this.$el.find("#comment-list"), n = this.getComments().filter(function (e) {
                return !e.parent
            });
            this.sortComments(n, t), e(n).each(function (e, t) {
                var n = i.find("> li.comment[data-id=" + t.id + "]");
                i.append(n)
            })
        },
        showActiveSort: function () {
            var e = this.$el.find('.navigation li[data-sort-key="' + this.currentSortKey + '"]');
            this.$el.find(".navigation li").removeClass("active"), e.addClass("active");
            var t = this.$el.find(".navigation .title");
            if ("attachments" != this.currentSortKey)t.addClass("active"), t.find("header").html(e.first().html()); else {
                var i = this.$el.find(".navigation ul.dropdown").children().first();
                t.find("header").html(i.html())
            }
            this.showActiveContainer()
        },
        forceResponsive: function () {
            this.$el.addClass("responsive")
        },
        closeDropdowns: function () {
            this.$el.find(".dropdown").hide()
        },
        saveOnKeydown: function (t) {
            if (13 == t.keyCode) {
                var i = t.metaKey || t.ctrlKey;
                (this.options.postCommentOnEnter || i) && (e(t.currentTarget).siblings(".control-row").find(".save").trigger("click"), t.stopPropagation(), t.preventDefault())
            }
        },
        saveEditableContent: function (t) {
            var i = e(t.currentTarget);
            i.data("before", i.html())
        },
        checkEditableContentForChange: function (t) {
            var i = e(t.currentTarget);
            i.data("before") != i.html() && (i.data("before", i.html()), i.trigger("change"))
        },
        navigationElementClicked: function (t) {
            var i = e(t.currentTarget).data().sortKey;
            "attachments" != i && this.sortAndReArrangeComments(i), this.currentSortKey = i, this.showActiveSort()
        },
        toggleNavigationDropdown: function (t) {
            t.stopPropagation(), e(t.currentTarget).find("~ .dropdown").toggle()
        },
        showMainCommentingField: function (t) {
            var i = e(t.currentTarget);
            i.siblings(".control-row").show(), i.parent().find(".close").show(), i.focus()
        },
        hideMainCommentingField: function (t) {
            var i = e(t.currentTarget), n = this.$el.find(".commenting-field.main .textarea"), a = this.$el.find(".commenting-field.main .control-row");
            this.clearTextarea(n), this.adjustTextareaHeight(n, !1), a.hide(), i.hide(), n.blur()
        },
        increaseTextareaHeight: function (t) {
            var i = e(t.currentTarget);
            this.adjustTextareaHeight(i, !0)
        },
        textareaContentChanged: function (t) {
            var i = e(t.currentTarget), n = i.siblings(".control-row").find(".save");
            if (!i.find(".reply-to.tag").length) {
                var a = i.attr("data-comment");
                if (a) {
                    var r = i.parents("li.comment");
                    if (r.length > 1) {
                        o = r.last().data("id");
                        i.attr("data-parent", o)
                    }
                } else {
                    var o = i.parents("li.comment").last().data("id");
                    i.attr("data-parent", o)
                }
            }
            var s = i.parents(".commenting-field").first();
            i[0].scrollHeight > i.outerHeight() ? s.addClass("scrollable") : s.removeClass("scrollable");
            var l = !0, c = this.getTextareaContent(i, !0);
            if (a = i.attr("data-comment")) {
                var d, u = c != this.commentsById[a].content;
                this.commentsById[a].parent && (d = this.commentsById[a].parent.toString());
                var p = i.attr("data-parent") != d;
                l = u || p
            }
            c.length && l ? n.addClass("enabled") : n.removeClass("enabled")
        },
        removeCommentingField: function (t) {
            var i = e(t.currentTarget);
            i.siblings(".textarea").attr("data-comment") && i.parents("li.comment").first().removeClass("edit"), i.parents(".commenting-field").first().remove()
        },
        postComment: function (t) {
            var i = this, n = e(t.currentTarget), a = n.parents(".commenting-field").first(), r = a.find(".textarea");
            n.removeClass("enabled");
            var o = this.createCommentJSON(r);
            o = this.applyExternalMappings(o);
            var s = function (e) {
                i.createComment(e), a.find(".close").trigger("click")
            }, l = function () {
                n.addClass("enabled")
            };
            this.options.postComment(o, s, l)
        },
        createComment: function (e) {
            var t = this.createCommentModel(e);
            this.addCommentToDataModel(t), this.addComment(t)
        },
        putComment: function (t) {
            var i = this, n = e(t.currentTarget), a = n.parents(".commenting-field").first(), r = a.find(".textarea");
            n.removeClass("enabled");
            var o = e.extend({}, this.commentsById[r.attr("data-comment")]);
            e.extend(o, {
                parent: r.attr("data-parent") || null,
                content: this.getTextareaContent(r),
                pings: this.getPings(r),
                modified: (new Date).getTime()
            }), o = this.applyExternalMappings(o);
            var s = function (e) {
                var t = i.createCommentModel(e);
                delete t.childs, i.updateCommentModel(t), a.find(".close").trigger("click"), i.reRenderComment(t.id)
            }, l = function () {
                n.addClass("enabled")
            };
            this.options.putComment(o, s, l)
        },
        deleteComment: function (t) {
            var i = this, n = e(t.currentTarget), a = n.parents(".comment").first(), r = e.extend({}, this.commentsById[a.attr("data-id")]), o = r.id, s = r.parent;
            n.removeClass("enabled"), r = this.applyExternalMappings(r);
            var l = function () {
                i.removeComment(o), s && i.reRenderCommentActionBar(s)
            }, c = function () {
                n.addClass("enabled")
            };
            this.options.deleteComment(r, l, c)
        },
        hashtagClicked: function (t) {
            var i = e(t.currentTarget).attr("data-value");
            this.options.hashtagClicked(i)
        },
        pingClicked: function (t) {
            var i = e(t.currentTarget).attr("data-value");
            this.options.pingClicked(i)
        },
        fileInputChanged: function (t, i) {
            var i = t.currentTarget.files, n = e(t.currentTarget).parents(".commenting-field").first();
            this.uploadAttachments(i, n)
        },
        upvoteComment: function (t) {
            var i, n = this, a = e(t.currentTarget).parents("li.comment").first().data().model, r = a.upvoteCount;
            i = a.userHasUpvoted ? r - 1 : r + 1, a.userHasUpvoted = !a.userHasUpvoted, a.upvoteCount = i, this.reRenderUpvotes(a.id);
            var o = e.extend({}, a);
            o = this.applyExternalMappings(o);
            var s = function (e) {
                var t = n.createCommentModel(e);
                n.updateCommentModel(t), n.reRenderUpvotes(t.id)
            }, l = function () {
                a.userHasUpvoted = !a.userHasUpvoted, a.upvoteCount = r, n.reRenderUpvotes(a.id)
            };
            this.options.upvoteComment(o, s, l)
        },
        toggleReplies: function (t) {
            var i = e(t.currentTarget);
            i.siblings(".hidden-reply").toggleClass("visible"), this.setToggleAllButtonText(i, !0)
        },
        replyButtonClicked: function (t) {
            var i = e(t.currentTarget), n = i.parents("li.comment").last(), a = i.parents(".comment").first().data().id, r = n.find(".child-comments > .commenting-field");
            if (r.length && r.remove(), r.find(".textarea").attr("data-parent") != a) {
                r = this.createCommentingFieldElement(a), n.find(".child-comments").append(r);
                var o = r.find(".textarea");
                this.moveCursorToEnd(o)
            }
        },
        editButtonClicked: function (t) {
            var i = e(t.currentTarget).parents("li.comment").first(), n = i.data().model;
            i.addClass("edit");
            var a = this.createCommentingFieldElement(n.parent, n.id);
            i.find(".comment-wrapper").first().append(a);
            var r = a.find(".textarea");
            r.attr("data-comment", n.id), r.append(this.getFormattedCommentContent(n, !0)), this.moveCursorToEnd(r)
        },
        showDroppableOverlay: function () {
            this.options.enableAttachments && (this.$el.find(".droppable-overlay").css("top", this.$el[0].scrollTop), this.$el.find(".droppable-overlay").show(), this.$el.addClass("drag-ongoing"))
        },
        handleDragEnter: function (t) {
            var i = e(t.currentTarget).data("dnd-count") || 0;
            i++, e(t.currentTarget).data("dnd-count", i), e(t.currentTarget).addClass("drag-over")
        },
        handleDragLeave: function (t, i) {
            var n = e(t.currentTarget).data("dnd-count");
            n--, e(t.currentTarget).data("dnd-count", n), 0 == n && (e(t.currentTarget).removeClass("drag-over"), i && i())
        },
        handleDragLeaveForOverlay: function (e) {
            var t = this;
            this.handleDragLeave(e, function () {
                t.hideDroppableOverlay()
            })
        },
        handleDragLeaveForDroppable: function (e) {
            this.handleDragLeave(e)
        },
        handleDragOverForOverlay: function (e) {
            e.stopPropagation(), e.preventDefault(), e.originalEvent.dataTransfer.dropEffect = "copy"
        },
        hideDroppableOverlay: function () {
            this.$el.find(".droppable-overlay").hide(), this.$el.removeClass("drag-ongoing")
        },
        handleDrop: function (t) {
            t.preventDefault(), e(t.target).trigger("dragleave"), this.hideDroppableOverlay(), this.uploadAttachments(t.originalEvent.dataTransfer.files)
        },
        stopPropagation: function (e) {
            e.stopPropagation()
        },
        createHTML: function () {
            var t = this.createCommentingFieldElement();
            t.addClass("main"), this.$el.append(t), t.find(".control-row").hide(), t.find(".close").hide(), this.options.enableNavigation && (this.$el.append(this.createNavigationElement()), this.showActiveSort());
            var i = this.createSpinner();
            this.$el.append(i);
            var n = e("<div/>", {class: "data-container", "data-container": "comments"});
            this.$el.append(n);
            var a = e("<div/>", {
                class: "no-comments no-data",
                text: this.options.textFormatter(this.options.noCommentsText)
            }), r = e("<i/>", {class: "fa fa-comments fa-2x"});
            if (this.options.noCommentsIconURL.length && (r.css("background-image", 'url("' + this.options.noCommentsIconURL + '")'), r.addClass("image")), a.prepend(e("<br/>")).prepend(r), n.append(a), this.options.enableAttachments) {
                var o = e("<div/>", {class: "data-container", "data-container": "attachments"});
                this.$el.append(o);
                var s = e("<div/>", {
                    class: "no-attachments no-data",
                    text: this.options.textFormatter(this.options.noAttachmentsText)
                }), l = e("<i/>", {class: "fa fa-paperclip fa-2x"});
                this.options.attachmentIconURL.length && (l.css("background-image", 'url("' + this.options.attachmentIconURL + '")'), l.addClass("image")), s.prepend(e("<br/>")).prepend(l), o.append(s);
                var c = e("<div/>", {class: "droppable-overlay"}), d = e("<div/>", {class: "droppable-container"}), u = e("<div/>", {class: "droppable"}), p = e("<i/>", {class: "fa fa-upload fa-4x"});
                this.options.uploadIconURL.length && (p.css("background-image", 'url("' + this.options.uploadIconURL + '")'), p.addClass("image"));
                var m = e("<div/>", {text: this.options.textFormatter(this.options.attachmentDropText)});
                u.append(p), u.append(m), c.html(d.html(u)).hide(), this.$el.append(c)
            }
        },
        createProfilePictureElement: function (t) {
            if (t)i = e("<img/>", {src: t}); else var i = e("<i/>", {class: "fa fa-user"});
            return i.addClass("profile-picture"), this.options.roundProfilePictures && i.addClass("round"), i
        },
        createCommentingFieldElement: function (t, i) {
            var n = this, a = e("<div/>", {class: "commenting-field"});
            if (i)r = this.commentsById[i].profilePictureURL; else var r = this.options.profilePictureURL;
            var o = this.createProfilePictureElement(r), s = e("<div/>", {class: "textarea-wrapper"}), l = e("<div/>", {class: "control-row"}), c = e("<div/>", {
                class: "textarea",
                "data-placeholder": this.options.textFormatter(this.options.textareaPlaceholderText),
                contenteditable: !0
            });
            this.adjustTextareaHeight(c, !1);
            var d = e("<span/>", {class: "close"}).append(e('<span class="left"/>')).append(e('<span class="right"/>'));
            if (i) {
                var u = this.options.textFormatter(this.options.saveText), p = e("<span/>", {
                    class: "delete",
                    text: this.options.textFormatter(this.options.deleteText)
                }).css("background-color", this.options.deleteButtonColor);
                l.append(p), this.isAllowedToDelete(i) && p.addClass("enabled")
            } else {
                u = this.options.textFormatter(this.options.sendText);
                if (this.options.enableAttachments) {
                    var m = e("<span/>", {class: "enabled upload"}), h = e("<i/>", {class: "fa fa-upload"}), f = e("<input/>", {
                        type: "file",
                        "data-role": "none"
                    });
                    e.browser.mobile || f.attr("multiple", "multiple"), this.options.uploadIconURL.length && (h.css("background-image", 'url("' + this.options.uploadIconURL + '")'), h.addClass("image")), m.append(h).append(f), l.append(m)
                }
            }
            var g = e("<span/>", {class: (i ? "update" : "send") + " save highlight-background", text: u});
            if (l.prepend(g), s.append(d).append(c).append(l), a.append(o).append(s), t) {
                c.attr("data-parent", t);
                var v = this.commentsById[t];
                if (v.parent) {
                    c.html("&nbsp;");
                    var y = "@" + v.fullname, b = this.createTagElement(y, "reply-to", v.creator);
                    c.prepend(b)
                }
            }
            return this.options.enablePinging && (c.textcomplete([{
                match: /(^|\s)@(([a-zÃ¤Ã¶Ã¼ÃŸ]|\s)*)$/im,
                search: function (t, i) {
                    t = n.normalizeSpaces(t);
                    var a = n.getPings(c), r = n.getUsers().filter(function (e) {
                        var t = e.id == n.options.currentUserId, i = -1 != a.indexOf(e.id);
                        return !t && !i
                    });
                    n.sortUsers(r), i(e.map(r, function (e) {
                        var i = t.toLowerCase();
                        return -1 != e.fullname.toLowerCase().indexOf(i) ? e : null
                    }))
                },
                template: function (t) {
                    var i = e("<div/>"), n = e("<img/>", {
                        src: t.profile_picture_url,
                        class: "profile-picture round"
                    }), a = e("<div/>", {class: "details"}), r = e("<div/>", {class: "name"}).html(t.fullname), o = e("<div/>", {class: "email"}).html(t.email);
                    return a.append(r).append(o), i.append(n).append(a), i.html()
                },
                replace: function (e) {
                    return " " + n.createTagElement("@" + e.fullname, "ping", e.id)[0].outerHTML + " "
                }
            }], {
                appendTo: ".jquery-comments",
                dropdownClassName: "dropdown autocomplete",
                maxCount: 5,
                rightEdgeOffset: 0
            }), c.on({
                "textComplete:show": function () {
                    var t = e(this).data("textComplete").dropdown.$el;
                    t.hide();
                    var i = function () {
                        return !t.is(":empty")
                    }, a = function () {
                        var e = parseInt(t.css("left"));
                        t.css("left", 0);
                        var i = n.$el.width() - t.width(), a = Math.min(i, e);
                        t.css("left", a), t.show()
                    };
                    n.waitUntil(i, a)
                }
            })), a
        },
        createNavigationElement: function () {
            var t = e("<ul/>", {class: "navigation"}), i = e("<div/>", {class: "navigation-wrapper"});
            t.append(i);
            var n = e("<li/>", {
                text: this.options.textFormatter(this.options.newestText),
                "data-sort-key": "newest",
                "data-container-name": "comments"
            }), a = e("<li/>", {
                text: this.options.textFormatter(this.options.oldestText),
                "data-sort-key": "oldest",
                "data-container-name": "comments"
            }), r = e("<li/>", {
                text: this.options.textFormatter(this.options.popularText),
                "data-sort-key": "popularity",
                "data-container-name": "comments"
            }), o = e("<li/>", {
                text: this.options.textFormatter(this.options.attachmentsText),
                "data-sort-key": "attachments",
                "data-container-name": "attachments"
            }), s = e("<i/>", {class: "fa fa-paperclip"});
            this.options.attachmentIconURL.length && (s.css("background-image", 'url("' + this.options.attachmentIconURL + '")'), s.addClass("image")), o.prepend(s);
            var l = e("<div/>", {class: "navigation-wrapper responsive"}), c = e("<ul/>", {class: "dropdown"}), d = e("<li/>", {class: "title"}), u = e("<header/>");
            return d.append(u), l.append(d), l.append(c), t.append(l), i.append(n).append(a), c.append(n.clone()).append(a.clone()), (this.options.enableReplying || this.options.enableUpvoting) && (i.append(r), c.append(r.clone())), this.options.enableAttachments && (i.append(o), l.append(o.clone())), this.options.forceResponsive && this.forceResponsive(), t
        },
        createSpinner: function () {
            var t = e("<div/>", {class: "spinner"}), i = e("<i/>", {class: "fa fa-spinner fa-spin"});
            return this.options.spinnerIconURL.length && (i.css("background-image", 'url("' + this.options.spinnerIconURL + '")'), i.addClass("image")), t.html(i), t
        },
        createCommentElement: function (t) {
            var i = e("<li/>", {"data-id": t.id, class: "comment"}).data("model", t);
            t.createdByCurrentUser && i.addClass("by-current-user"), t.createdByAdmin && i.addClass("by-admin");
            var n = e("<ul/>", {class: "child-comments"}), a = this.createCommentWrapperElement(t);
            return i.append(a), null == t.parent && i.append(n), i
        },
        createCommentWrapperElement: function (t) {
            var i = e("<div/>", {class: "comment-wrapper"}), n = this.createProfilePictureElement(t.profilePictureURL), a = e("<time/>", {
                text: this.options.timeFormatter(t.created),
                "data-original": t.created
            }), r = t.createdByCurrentUser ? this.options.textFormatter(this.options.youText) : t.fullname, o = e("<div/>", {class: "name"});
            if (t.profileURL) {
                g = e("<a/>", {href: t.profileURL, text: r});
                o.html(g)
            } else o.text(r);
            if ((t.createdByCurrentUser || t.createdByAdmin) && o.addClass("highlight-font-bold"), t.parent) {
                var s = this.commentsById[t.parent];
                if (s.parent) {
                    var l = e("<span/>", {class: "reply-to", text: s.fullname}), c = e("<i/>", {class: "fa fa-share"});
                    this.options.replyIconURL.length && (c.css("background-image", 'url("' + this.options.replyIconURL + '")'), c.addClass("image")), l.prepend(c), o.append(l)
                }
            }
            var d = e("<div/>", {class: "wrapper"}), u = e("<div/>", {class: "content"}), p = void 0 != t.fileURL;
            if (p) {
                var m = null, h = null;
                if (t.fileMimeType) {
                    var f = t.fileMimeType.split("/");
                    2 == f.length && (m = f[1], h = f[0])
                }
                var g = e("<a/>", {class: "attachment", href: t.fileURL, target: "_blank"});
                if ("image" == h) {
                    var v = e("<img/>", {src: t.fileURL});
                    g.html(v)
                } else if ("video" == h) {
                    var y = e("<video/>", {src: t.fileURL, type: t.fileMimeType, controls: "controls"});
                    g.html(y)
                } else {
                    var b = ["archive", "audio", "code", "excel", "image", "movie", "pdf", "photo", "picture", "powerpoint", "sound", "video", "word", "zip"], _ = "fa fa-file-o";
                    b.indexOf(m) > 0 ? _ = "fa fa-file-" + m + "-o" : b.indexOf(h) > 0 && (_ = "fa fa-file-" + h + "-o");
                    var C = e("<i/>", {class: _});
                    this.options.fileIconURL.length && (C.css("background-image", 'url("' + this.options.fileIconURL + '")'), C.addClass("image"));
                    var w = t.fileURL.split("/"), x = w[w.length - 1];
                    x = x.split("?")[0], x = decodeURIComponent(x), g.text(x), g.prepend(C)
                }
                u.html(g)
            } else u.html(this.getFormattedCommentContent(t));
            if (t.modified && t.modified != t.created) {
                var k = this.options.timeFormatter(t.modified), T = e("<time/>", {
                    class: "edited",
                    text: this.options.textFormatter(this.options.editedText) + " " + k,
                    "data-original": t.modified
                });
                u.append(T)
            }
            var E = e("<span/>", {class: "actions"}), F = e("<span/>", {
                class: "separator",
                text: "Â·"
            }), I = e("<button/>", {
                class: "action reply",
                type: "button",
                text: this.options.textFormatter(this.options.replyText)
            }), j = e("<i/>", {class: "fa fa-thumbs-up"});
            this.options.upvoteIconURL.length && (j.css("background-image", 'url("' + this.options.upvoteIconURL + '")'), j.addClass("image"));
            var L = this.createUpvoteElement(t);
            if (this.options.enableReplying && E.append(I), this.options.enableUpvoting && E.append(L), t.createdByCurrentUser || this.options.currentUserIsAdmin)if (p && this.isAllowedToDelete(t.id)) {
                var A = e("<button/>", {
                    class: "action delete enabled",
                    text: this.options.textFormatter(this.options.deleteText)
                });
                E.append(A)
            } else if (!p && this.options.enableEditing) {
                var z = e("<button/>", {class: "action edit", text: this.options.textFormatter(this.options.editText)});
                E.append(z)
            }
            return E.children().each(function (t, i) {
                e(i).is(":last-child") || e(i).after(F.clone())
            }), d.append(u), d.append(E), i.append(n).append(a).append(o).append(d), i
        },
        createUpvoteElement: function (t) {
            var i = e("<i/>", {class: "fa fa-thumbs-up"});
            return this.options.upvoteIconURL.length && (i.css("background-image", 'url("' + this.options.upvoteIconURL + '")'), i.addClass("image")), e("<button/>", {class: "action upvote" + (t.userHasUpvoted ? " highlight-font" : "")}).append(e("<span/>", {
                text: t.upvoteCount,
                class: "upvote-count"
            })).append(i)
        },
        createTagElement: function (t, i, n) {
            var a = e("<input/>", {class: "tag", type: "button"});
            return i && a.addClass(i), a.val(t), a.attr("data-value", n), a
        },
        reRenderComment: function (t) {
            var i = this.commentsById[t], n = this;
            this.$el.find('li.comment[data-id="' + i.id + '"]').each(function (t, a) {
                var r = n.createCommentWrapperElement(i);
                e(a).find(".comment-wrapper").first().replaceWith(r)
            })
        },
        reRenderCommentActionBar: function (t) {
            var i = this.commentsById[t], n = this;
            this.$el.find('li.comment[data-id="' + i.id + '"]').each(function (t, a) {
                var r = n.createCommentWrapperElement(i);
                e(a).find(".actions").first().replaceWith(r.find(".actions"))
            })
        },
        reRenderUpvotes: function (t) {
            var i = this.commentsById[t], n = this;
            this.$el.find('li.comment[data-id="' + i.id + '"]').each(function (t, a) {
                var r = n.createUpvoteElement(i);
                e(a).find(".upvote").first().replaceWith(r)
            })
        },
        createCssDeclarations: function () {
            e("head style.jquery-comments-css").remove(), this.createCss(".jquery-comments ul.navigation li.active:after {background: " + this.options.highlightColor + " !important;", NaN), this.createCss(".jquery-comments ul.navigation ul.dropdown li.active {background: " + this.options.highlightColor + " !important;", NaN), this.createCss(".jquery-comments .highlight-background {background: " + this.options.highlightColor + " !important;", NaN), this.createCss(".jquery-comments .highlight-font {color: " + this.options.highlightColor + " !important;}"), this.createCss(".jquery-comments .highlight-font-bold {color: " + this.options.highlightColor + " !important;font-weight: bold;}")
        },
        createCss: function (t) {
            var i = e("<style/>", {type: "text/css", class: "jquery-comments-css", text: t});
            e("head").append(i)
        },
        getComments: function () {
            var e = this;
            return Object.keys(this.commentsById).map(function (t) {
                return e.commentsById[t]
            })
        },
        getUsers: function () {
            var e = this;
            return Object.keys(this.usersById).map(function (t) {
                return e.usersById[t]
            })
        },
        getChildComments: function (e) {
            return this.getComments().filter(function (t) {
                return t.parent == e
            })
        },
        getAttachments: function () {
            return this.getComments().filter(function (e) {
                return void 0 != e.fileURL
            })
        },
        getOutermostParent: function (e) {
            var t = e;
            do {
                var i = this.commentsById[t];
                t = i.parent
            } while (null != i.parent);
            return i
        },
        createCommentJSON: function (e) {
            var t = (new Date).toISOString();
            return {
                id: "c" + (this.getComments().length + 1),
                parent: e.attr("data-parent") || null,
                created: t,
                modified: t,
                content: this.getTextareaContent(e),
                pings: this.getPings(e),
                fullname: this.options.textFormatter(this.options.youText),
                profilePictureURL: this.options.profilePictureURL,
                createdByCurrentUser: !0,
                upvoteCount: 0,
                userHasUpvoted: !1
            }
        },
        isAllowedToDelete: function (t) {
            if (this.options.enableDeleting) {
                var i = !0;
                return this.options.enableDeletingCommentWithReplies || e(this.getComments()).each(function (e, n) {
                    n.parent == t && (i = !1)
                }), i
            }
            return !1
        },
        setToggleAllButtonText: function (e, t) {
            var i = this, n = e.find("span.text"), a = e.find(".caret"), r = function () {
                var t = i.options.textFormatter(i.options.viewAllRepliesText), a = e.siblings(".comment").length;
                t = t.replace("__replyCount__", a), n.text(t)
            }, o = this.options.textFormatter(this.options.hideRepliesText);
            t ? (n.text() == o ? r() : n.text(o), a.toggleClass("up")) : n.text() != o && r()
        },
        adjustTextareaHeight: function (t, i) {
            t = e(t);
            var n = 1 == i ? this.options.textareaRowsOnFocus : this.options.textareaRows;
            do {
                (function (e) {
                    var i = 2.2 + 1.45 * (e - 1);
                    t.css("height", i + "em")
                })(n), n++;
                var a = t[0].scrollHeight > t.outerHeight(), r = 0 != this.options.textareaMaxRows && n > this.options.textareaMaxRows
            } while (a && !r)
        },
        clearTextarea: function (e) {
            e.empty().trigger("input")
        },
        getTextareaContent: function (t, i) {
            var n = t.clone();
            n.find(".reply-to.tag").remove(), n.find(".tag.hashtag").replaceWith(function () {
                return i ? e(this).val() : "#" + e(this).attr("data-value")
            }), n.find(".tag.ping").replaceWith(function () {
                return i ? e(this).val() : "@" + e(this).attr("data-value")
            });
            var a = e("<pre/>").html(n.html());
            return a.find("div, p, br").replaceWith(function () {
                return "\n" + this.innerHTML
            }), a.text().replace(/^\s+/g, "")
        },
        getFormattedCommentContent: function (e, t) {
            var i = this.escape(e.content);
            return i = this.linkify(i), i = this.highlightTags(e, i), t && (i = i.replace(/(?:\n)/g, "<br>")), i
        },
        getPings: function (t) {
            return e.map(t.find(".ping"), function (t) {
                return parseInt(e(t).attr("data-value"))
            })
        },
        moveCursorToEnd: function (t) {
            if (t = e(t)[0], e(t).trigger("input"), e(t).scrollTop(t.scrollHeight), void 0 !== window.getSelection && void 0 !== document.createRange) {
                var i = document.createRange();
                i.selectNodeContents(t), i.collapse(!1);
                var n = window.getSelection();
                n.removeAllRanges(), n.addRange(i)
            } else if (void 0 !== document.body.createTextRange) {
                var a = document.body.createTextRange();
                a.moveToElementText(t), a.collapse(!1), a.select()
            }
            t.focus()
        },
        escape: function (t) {
            return e("<pre/>").text(this.normalizeSpaces(t)).html()
        },
        normalizeSpaces: function (e) {
            return e ? e.replace("Â ", " ") : void 0
        },
        after: function (e, t) {
            var i = this;
            return function () {
                return e--, 0 == e ? t.apply(i, arguments) : void 0
            }
        },
        highlightTags: function (e, t) {
            return this.options.enableHashtags && (t = this.highlightHashtags(e, t)), this.options.enablePinging && (t = this.highlightPings(e, t)), t
        },
        highlightHashtags: function (e, t) {
            var i = this;
            if (-1 != t.indexOf("#")) {
                var n = function (e) {
                    return (e = i.createTagElement("#" + e, "hashtag", e))[0].outerHTML
                }, a = /(^|\s)#([a-zÃ¤Ã¶Ã¼ÃŸ\d-_]+)/gim;
                t = t.replace(a, function (e, t, i) {
                    return t + n(i)
                })
            }
            return t
        },
        highlightPings: function (t, i) {
            var n = this;
            if (-1 != i.indexOf("@")) {
                var a = function (e) {
                    return n.createTagElement("@" + e.fullname, "ping", e.id)[0].outerHTML
                };
                e(t.pings).each(function (e, t) {
                    if (t in n.usersById) {
                        var r = n.usersById[t], o = "@" + r.fullname;
                        i = i.replace(o, a(r))
                    }
                })
            }
            return i
        },
        linkify: function (e) {
            var t, i, n, a;
            if (i = /(^|\s)((https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim, n = /(^|\s)(www\.[\S]+(\b|$))/gim, a = /(^|\s)(([a-zA-Z0-9\-\_\.]+)@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim, t = (t = (t = e.replace(i, '$1<a href="$2" target="_blank">$2</a>')).replace(n, '$1<a href="http://$2" target="_blank">$2</a>')).replace(a, '$1<a href="mailto:$2">$2</a>'), (e.match(/<a href/g) || []).length > 0) {
                for (var r = e.split(/(<\/a>)/g), o = 0; o < r.length; o++)null == r[o].match(/<a href/g) && (r[o] = r[o].replace(i, '<a href="$1" target="_blank">$1</a>').replace(n, '$1<a href="http://$2" target="_blank">$2</a>').replace(a, '<a href="mailto:$1">$1</a>'));
                return r.join("")
            }
            return t
        },
        waitUntil: function (e, t) {
            var i = this;
            e() ? t() : setTimeout(function () {
                i.waitUntil(e, t)
            }, 100)
        },
        applyInternalMappings: function (e) {
            var t = {}, i = this.options.fieldMappings;
            for (var n in i)i.hasOwnProperty(n) && (t[i[n]] = n);
            return this.applyMappings(t, e)
        },
        applyExternalMappings: function (e) {
            var t = this.options.fieldMappings;
            return this.applyMappings(t, e)
        },
        applyMappings: function (e, t) {
            var i = {};
            for (var n in t)n in e && (i[e[n]] = t[n]);
            return i
        }
    };
    e.fn.comments = function (i) {
        return this.each(function () {
            var n = Object.create(t);
            e.data(this, "comments", n), n.init(i || {}, this)
        })
    }
}), function (e) {
    "function" == typeof define && define.amd ? define(["jquery"], e) : e("object" == typeof exports ? require("jquery") : window.jQuery || window.Zepto)
}(function (e) {
    var t, i, n, a, r, o, s = "Close", l = "BeforeClose", c = "MarkupParse", d = "Open", u = "Change", p = "mfp", m = "." + p, h = "mfp-ready", f = "mfp-removing", g = "mfp-prevent-close", v = function () {
    }, y = !!window.jQuery, b = e(window), _ = function (e, i) {
        t.ev.on(p + e + m, i)
    }, C = function (t, i, n, a) {
        var r = document.createElement("div");
        return r.className = "mfp-" + t, n && (r.innerHTML = n), a ? i && i.appendChild(r) : (r = e(r), i && r.appendTo(i)), r
    }, w = function (i, n) {
        t.ev.triggerHandler(p + i, n), t.st.callbacks && (i = i.charAt(0).toLowerCase() + i.slice(1), t.st.callbacks[i] && t.st.callbacks[i].apply(t, e.isArray(n) ? n : [n]))
    }, x = function (i) {
        return i === o && t.currTemplate.closeBtn || (t.currTemplate.closeBtn = e(t.st.closeMarkup.replace("%title%", t.st.tClose)), o = i), t.currTemplate.closeBtn
    }, k = function () {
        e.magnificPopup.instance || ((t = new v).init(), e.magnificPopup.instance = t)
    }, T = function () {
        var e = document.createElement("p").style, t = ["ms", "O", "Moz", "Webkit"];
        if (void 0 !== e.transition)return !0;
        for (; t.length;)if (t.pop() + "Transition"in e)return !0;
        return !1
    };
    v.prototype = {
        constructor: v, init: function () {
            var i = navigator.appVersion;
            t.isLowIE = t.isIE8 = document.all && !document.addEventListener, t.isAndroid = /android/gi.test(i), t.isIOS = /iphone|ipad|ipod/gi.test(i), t.supportsTransition = T(), t.probablyMobile = t.isAndroid || t.isIOS || /(Opera Mini)|Kindle|webOS|BlackBerry|(Opera Mobi)|(Windows Phone)|IEMobile/i.test(navigator.userAgent), n = e(document), t.popupsCache = {}
        }, open: function (i) {
            var a;
            if (!1 === i.isObj) {
                t.items = i.items.toArray(), t.index = 0;
                var o, s = i.items;
                for (a = 0; a < s.length; a++)if ((o = s[a]).parsed && (o = o.el[0]), o === i.el[0]) {
                    t.index = a;
                    break
                }
            } else t.items = e.isArray(i.items) ? i.items : [i.items], t.index = i.index || 0;
            {
                if (!t.isOpen) {
                    t.types = [], r = "", i.mainEl && i.mainEl.length ? t.ev = i.mainEl.eq(0) : t.ev = n, i.key ? (t.popupsCache[i.key] || (t.popupsCache[i.key] = {}), t.currTemplate = t.popupsCache[i.key]) : t.currTemplate = {}, t.st = e.extend(!0, {}, e.magnificPopup.defaults, i), t.fixedContentPos = "auto" === t.st.fixedContentPos ? !t.probablyMobile : t.st.fixedContentPos, t.st.modal && (t.st.closeOnContentClick = !1, t.st.closeOnBgClick = !1, t.st.showCloseBtn = !1, t.st.enableEscapeKey = !1), t.bgOverlay || (t.bgOverlay = C("bg").on("click" + m, function () {
                        t.close()
                    }), t.wrap = C("wrap").attr("tabindex", -1).on("click" + m, function (e) {
                        t._checkIfClose(e.target) && t.close()
                    }), t.container = C("container", t.wrap)), t.contentContainer = C("content"), t.st.preloader && (t.preloader = C("preloader", t.container, t.st.tLoading));
                    var l = e.magnificPopup.modules;
                    for (a = 0; a < l.length; a++) {
                        var u = l[a];
                        u = u.charAt(0).toUpperCase() + u.slice(1), t["init" + u].call(t)
                    }
                    w("BeforeOpen"), t.st.showCloseBtn && (t.st.closeBtnInside ? (_(c, function (e, t, i, n) {
                        i.close_replaceWith = x(n.type)
                    }), r += " mfp-close-btn-in") : t.wrap.append(x())), t.st.alignTop && (r += " mfp-align-top"), t.fixedContentPos ? t.wrap.css({
                        overflow: t.st.overflowY,
                        overflowX: "hidden",
                        overflowY: t.st.overflowY
                    }) : t.wrap.css({
                        top: b.scrollTop(),
                        position: "absolute"
                    }), (!1 === t.st.fixedBgPos || "auto" === t.st.fixedBgPos && !t.fixedContentPos) && t.bgOverlay.css({
                        height: n.height(),
                        position: "absolute"
                    }), t.st.enableEscapeKey && n.on("keyup" + m, function (e) {
                        27 === e.keyCode && t.close()
                    }), b.on("resize" + m, function () {
                        t.updateSize()
                    }), t.st.closeOnContentClick || (r += " mfp-auto-cursor"), r && t.wrap.addClass(r);
                    var p = t.wH = b.height(), f = {};
                    if (t.fixedContentPos && t._hasScrollBar(p)) {
                        var g = t._getScrollbarSize();
                        g && (f.marginRight = g)
                    }
                    t.fixedContentPos && (t.isIE7 ? e("body, html").css("overflow", "hidden") : f.overflow = "hidden");
                    var v = t.st.mainClass;
                    return t.isIE7 && (v += " mfp-ie7"), v && t._addClassToMFP(v), t.updateItemHTML(), w("BuildControls"), e("html").css(f), t.bgOverlay.add(t.wrap).prependTo(t.st.prependTo || e(document.body)), t._lastFocusedEl = document.activeElement, setTimeout(function () {
                        t.content ? (t._addClassToMFP(h), t._setFocus()) : t.bgOverlay.addClass(h), n.on("focusin" + m, t._onFocusIn)
                    }, 16), t.isOpen = !0, t.updateSize(p), w(d), i
                }
                t.updateItemHTML()
            }
        }, close: function () {
            t.isOpen && (w(l), t.isOpen = !1, t.st.removalDelay && !t.isLowIE && t.supportsTransition ? (t._addClassToMFP(f), setTimeout(function () {
                t._close()
            }, t.st.removalDelay)) : t._close())
        }, _close: function () {
            w(s);
            var i = f + " " + h + " ";
            if (t.bgOverlay.detach(), t.wrap.detach(), t.container.empty(), t.st.mainClass && (i += t.st.mainClass + " "), t._removeClassFromMFP(i), t.fixedContentPos) {
                var a = {marginRight: ""};
                t.isIE7 ? e("body, html").css("overflow", "") : a.overflow = "", e("html").css(a)
            }
            n.off("keyup.mfp focusin" + m), t.ev.off(m), t.wrap.attr("class", "mfp-wrap").removeAttr("style"), t.bgOverlay.attr("class", "mfp-bg"), t.container.attr("class", "mfp-container"), !t.st.showCloseBtn || t.st.closeBtnInside && !0 !== t.currTemplate[t.currItem.type] || t.currTemplate.closeBtn && t.currTemplate.closeBtn.detach(), t.st.autoFocusLast && t._lastFocusedEl && e(t._lastFocusedEl).focus(), t.currItem = null, t.content = null, t.currTemplate = null, t.prevHeight = 0, w("AfterClose")
        }, updateSize: function (e) {
            if (t.isIOS) {
                var i = document.documentElement.clientWidth / window.innerWidth, n = window.innerHeight * i;
                t.wrap.css("height", n), t.wH = n
            } else t.wH = e || b.height();
            t.fixedContentPos || t.wrap.css("height", t.wH), w("Resize")
        }, updateItemHTML: function () {
            var i = t.items[t.index];
            t.contentContainer.detach(), t.content && t.content.detach(), i.parsed || (i = t.parseEl(t.index));
            var n = i.type;
            if (w("BeforeChange", [t.currItem ? t.currItem.type : "", n]), t.currItem = i, !t.currTemplate[n]) {
                var r = !!t.st[n] && t.st[n].markup;
                w("FirstMarkupParse", r), t.currTemplate[n] = !r || e(r)
            }
            a && a !== i.type && t.container.removeClass("mfp-" + a + "-holder");
            var o = t["get" + n.charAt(0).toUpperCase() + n.slice(1)](i, t.currTemplate[n]);
            t.appendContent(o, n), i.preloaded = !0, w(u, i), a = i.type, t.container.prepend(t.contentContainer), w("AfterChange")
        }, appendContent: function (e, i) {
            t.content = e, e ? t.st.showCloseBtn && t.st.closeBtnInside && !0 === t.currTemplate[i] ? t.content.find(".mfp-close").length || t.content.append(x()) : t.content = e : t.content = "", w("BeforeAppend"), t.container.addClass("mfp-" + i + "-holder"), t.contentContainer.append(t.content)
        }, parseEl: function (i) {
            var n, a = t.items[i];
            if (a.tagName ? a = {el: e(a)} : (n = a.type, a = {data: a, src: a.src}), a.el) {
                for (var r = t.types, o = 0; o < r.length; o++)if (a.el.hasClass("mfp-" + r[o])) {
                    n = r[o];
                    break
                }
                a.src = a.el.attr("data-mfp-src"), a.src || (a.src = a.el.attr("href"))
            }
            return a.type = n || t.st.type || "inline", a.index = i, a.parsed = !0, t.items[i] = a, w("ElementParse", a), t.items[i]
        }, addGroup: function (e, i) {
            var n = function (n) {
                n.mfpEl = this, t._openClick(n, e, i)
            };
            i || (i = {});
            var a = "click.magnificPopup";
            i.mainEl = e, i.items ? (i.isObj = !0, e.off(a).on(a, n)) : (i.isObj = !1, i.delegate ? e.off(a).on(a, i.delegate, n) : (i.items = e, e.off(a).on(a, n)))
        }, _openClick: function (i, n, a) {
            if ((void 0 !== a.midClick ? a.midClick : e.magnificPopup.defaults.midClick) || !(2 === i.which || i.ctrlKey || i.metaKey || i.altKey || i.shiftKey)) {
                var r = void 0 !== a.disableOn ? a.disableOn : e.magnificPopup.defaults.disableOn;
                if (r)if (e.isFunction(r)) {
                    if (!r.call(t))return !0
                } else if (b.width() < r)return !0;
                i.type && (i.preventDefault(), t.isOpen && i.stopPropagation()), a.el = e(i.mfpEl), a.delegate && (a.items = n.find(a.delegate)), t.open(a)
            }
        }, updateStatus: function (e, n) {
            if (t.preloader) {
                i !== e && t.container.removeClass("mfp-s-" + i), n || "loading" !== e || (n = t.st.tLoading);
                var a = {status: e, text: n};
                w("UpdateStatus", a), e = a.status, n = a.text, t.preloader.html(n), t.preloader.find("a").on("click", function (e) {
                    e.stopImmediatePropagation()
                }), t.container.addClass("mfp-s-" + e), i = e
            }
        }, _checkIfClose: function (i) {
            if (!e(i).hasClass(g)) {
                var n = t.st.closeOnContentClick, a = t.st.closeOnBgClick;
                if (n && a)return !0;
                if (!t.content || e(i).hasClass("mfp-close") || t.preloader && i === t.preloader[0])return !0;
                if (i === t.content[0] || e.contains(t.content[0], i)) {
                    if (n)return !0
                } else if (a && e.contains(document, i))return !0;
                return !1
            }
        }, _addClassToMFP: function (e) {
            t.bgOverlay.addClass(e), t.wrap.addClass(e)
        }, _removeClassFromMFP: function (e) {
            this.bgOverlay.removeClass(e), t.wrap.removeClass(e)
        }, _hasScrollBar: function (e) {
            return (t.isIE7 ? n.height() : document.body.scrollHeight) > (e || b.height())
        }, _setFocus: function () {
            (t.st.focus ? t.content.find(t.st.focus).eq(0) : t.wrap).focus()
        }, _onFocusIn: function (i) {
            return i.target === t.wrap[0] || e.contains(t.wrap[0], i.target) ? void 0 : (t._setFocus(), !1)
        }, _parseMarkup: function (t, i, n) {
            var a;
            n.data && (i = e.extend(n.data, i)), w(c, [t, i, n]), e.each(i, function (i, n) {
                if (void 0 === n || !1 === n)return !0;
                if ((a = i.split("_")).length > 1) {
                    var r = t.find(m + "-" + a[0]);
                    if (r.length > 0) {
                        var o = a[1];
                        "replaceWith" === o ? r[0] !== n[0] && r.replaceWith(n) : "img" === o ? r.is("img") ? r.attr("src", n) : r.replaceWith(e("<img>").attr("src", n).attr("class", r.attr("class"))) : r.attr(a[1], n)
                    }
                } else t.find(m + "-" + i).html(n)
            })
        }, _getScrollbarSize: function () {
            if (void 0 === t.scrollbarSize) {
                var e = document.createElement("div");
                e.style.cssText = "width: 99px; height: 99px; overflow: scroll; position: absolute; top: -9999px;", document.body.appendChild(e), t.scrollbarSize = e.offsetWidth - e.clientWidth, document.body.removeChild(e)
            }
            return t.scrollbarSize
        }
    }, e.magnificPopup = {
        instance: null,
        proto: v.prototype,
        modules: [],
        open: function (t, i) {
            return k(), t = t ? e.extend(!0, {}, t) : {}, t.isObj = !0, t.index = i || 0, this.instance.open(t)
        },
        close: function () {
            return e.magnificPopup.instance && e.magnificPopup.instance.close()
        },
        registerModule: function (t, i) {
            i.options && (e.magnificPopup.defaults[t] = i.options), e.extend(this.proto, i.proto), this.modules.push(t)
        },
        defaults: {
            disableOn: 0,
            key: null,
            midClick: !1,
            mainClass: "",
            preloader: !0,
            focus: "",
            closeOnContentClick: !1,
            closeOnBgClick: !0,
            closeBtnInside: !0,
            showCloseBtn: !0,
            enableEscapeKey: !0,
            modal: !1,
            alignTop: !1,
            removalDelay: 0,
            prependTo: null,
            fixedContentPos: "auto",
            fixedBgPos: "auto",
            overflowY: "auto",
            closeMarkup: '<button title="%title%" type="button" class="mfp-close">&#215;</button>',
            tClose: "Close (Esc)",
            tLoading: "Loading...",
            autoFocusLast: !0
        }
    }, e.fn.magnificPopup = function (i) {
        k();
        var n = e(this);
        if ("string" == typeof i)if ("open" === i) {
            var a, r = y ? n.data("magnificPopup") : n[0].magnificPopup, o = parseInt(arguments[1], 10) || 0;
            r.items ? a = r.items[o] : (a = n, r.delegate && (a = a.find(r.delegate)), a = a.eq(o)), t._openClick({mfpEl: a}, n, r)
        } else t.isOpen && t[i].apply(t, Array.prototype.slice.call(arguments, 1)); else i = e.extend(!0, {}, i), y ? n.data("magnificPopup", i) : n[0].magnificPopup = i, t.addGroup(n, i);
        return n
    };
    var E, F, I, j = "inline", L = function () {
        I && (F.after(I.addClass(E)).detach(), I = null)
    };
    e.magnificPopup.registerModule(j, {
        options: {hiddenClass: "hide", markup: "", tNotFound: "Content not found"},
        proto: {
            initInline: function () {
                t.types.push(j), _(s + "." + j, function () {
                    L()
                })
            }, getInline: function (i, n) {
                if (L(), i.src) {
                    var a = t.st.inline, r = e(i.src);
                    if (r.length) {
                        var o = r[0].parentNode;
                        o && o.tagName && (F || (E = a.hiddenClass, F = C(E), E = "mfp-" + E), I = r.after(F).detach().removeClass(E)), t.updateStatus("ready")
                    } else t.updateStatus("error", a.tNotFound), r = e("<div>");
                    return i.inlineElement = r, r
                }
                return t.updateStatus("ready"), t._parseMarkup(n, {}, i), n
            }
        }
    });
    var A, z = "ajax", D = function () {
        A && e(document.body).removeClass(A)
    }, P = function () {
        D(), t.req && t.req.abort()
    };
    e.magnificPopup.registerModule(z, {
        options: {
            settings: null,
            cursor: "mfp-ajax-cur",
            tError: '<a href="%url%">The content</a> could not be loaded.'
        }, proto: {
            initAjax: function () {
                t.types.push(z), A = t.st.ajax.cursor, _(s + "." + z, P), _("BeforeChange." + z, P)
            }, getAjax: function (i) {
                A && e(document.body).addClass(A), t.updateStatus("loading");
                var n = e.extend({
                    url: i.src, success: function (n, a, r) {
                        var o = {data: n, xhr: r};
                        w("ParseAjax", o), t.appendContent(e(o.data), z), i.finished = !0, D(), t._setFocus(), setTimeout(function () {
                            t.wrap.addClass(h)
                        }, 16), t.updateStatus("ready"), w("AjaxContentAdded")
                    }, error: function () {
                        D(), i.finished = i.loadError = !0, t.updateStatus("error", t.st.ajax.tError.replace("%url%", i.src))
                    }
                }, t.st.ajax.settings);
                return t.req = e.ajax(n), ""
            }
        }
    });
    var S, U = function (i) {
        if (i.data && void 0 !== i.data.title)return i.data.title;
        var n = t.st.image.titleSrc;
        if (n) {
            if (e.isFunction(n))return n.call(t, i);
            if (i.el)return i.el.attr(n) || ""
        }
        return ""
    };
    e.magnificPopup.registerModule("image", {
        options: {
            markup: '<div class="mfp-figure"><div class="mfp-close"></div><figure><div class="mfp-img"></div><figcaption><div class="mfp-bottom-bar"><div class="mfp-title"></div><div class="mfp-counter"></div></div></figcaption></figure></div>',
            cursor: "mfp-zoom-out-cur",
            titleSrc: "title",
            verticalFit: !0,
            tError: '<a href="%url%">The image</a> could not be loaded.'
        }, proto: {
            initImage: function () {
                var i = t.st.image, n = ".image";
                t.types.push("image"), _(d + n, function () {
                    "image" === t.currItem.type && i.cursor && e(document.body).addClass(i.cursor)
                }), _(s + n, function () {
                    i.cursor && e(document.body).removeClass(i.cursor), b.off("resize" + m)
                }), _("Resize" + n, t.resizeImage), t.isLowIE && _("AfterChange", t.resizeImage)
            }, resizeImage: function () {
                var e = t.currItem;
                if (e && e.img && t.st.image.verticalFit) {
                    var i = 0;
                    t.isLowIE && (i = parseInt(e.img.css("padding-top"), 10) + parseInt(e.img.css("padding-bottom"), 10)), e.img.css("max-height", t.wH - i)
                }
            }, _onImageHasSize: function (e) {
                e.img && (e.hasSize = !0, S && clearInterval(S), e.isCheckingImgSize = !1, w("ImageHasSize", e), e.imgHidden && (t.content && t.content.removeClass("mfp-loading"), e.imgHidden = !1))
            }, findImageSize: function (e) {
                var i = 0, n = e.img[0], a = function (r) {
                    S && clearInterval(S), S = setInterval(function () {
                        return n.naturalWidth > 0 ? void t._onImageHasSize(e) : (i > 200 && clearInterval(S), i++, void(3 === i ? a(10) : 40 === i ? a(50) : 100 === i && a(500)))
                    }, r)
                };
                a(1)
            }, getImage: function (i, n) {
                var a = 0, r = function () {
                    i && (i.img[0].complete ? (i.img.off(".mfploader"), i === t.currItem && (t._onImageHasSize(i), t.updateStatus("ready")), i.hasSize = !0, i.loaded = !0, w("ImageLoadComplete")) : (a++, 200 > a ? setTimeout(r, 100) : o()))
                }, o = function () {
                    i && (i.img.off(".mfploader"), i === t.currItem && (t._onImageHasSize(i), t.updateStatus("error", s.tError.replace("%url%", i.src))), i.hasSize = !0, i.loaded = !0, i.loadError = !0)
                }, s = t.st.image, l = n.find(".mfp-img");
                if (l.length) {
                    var c = document.createElement("img");
                    c.className = "mfp-img", i.el && i.el.find("img").length && (c.alt = i.el.find("img").attr("alt")), i.img = e(c).on("load.mfploader", r).on("error.mfploader", o), c.src = i.src, l.is("img") && (i.img = i.img.clone()), (c = i.img[0]).naturalWidth > 0 ? i.hasSize = !0 : c.width || (i.hasSize = !1)
                }
                return t._parseMarkup(n, {
                    title: U(i),
                    img_replaceWith: i.img
                }, i), t.resizeImage(), i.hasSize ? (S && clearInterval(S), i.loadError ? (n.addClass("mfp-loading"), t.updateStatus("error", s.tError.replace("%url%", i.src))) : (n.removeClass("mfp-loading"), t.updateStatus("ready")), n) : (t.updateStatus("loading"), i.loading = !0, i.hasSize || (i.imgHidden = !0, n.addClass("mfp-loading"), t.findImageSize(i)), n)
            }
        }
    });
    var R, M = function () {
        return void 0 === R && (R = void 0 !== document.createElement("p").style.MozTransform), R
    };
    e.magnificPopup.registerModule("zoom", {
        options: {
            enabled: !1,
            easing: "ease-in-out",
            duration: 300,
            opener: function (e) {
                return e.is("img") ? e : e.find("img")
            }
        }, proto: {
            initZoom: function () {
                var e, i = t.st.zoom, n = ".zoom";
                if (i.enabled && t.supportsTransition) {
                    var a, r, o = i.duration, c = function (e) {
                        var t = e.clone().removeAttr("style").removeAttr("class").addClass("mfp-animated-image"), n = "all " + i.duration / 1e3 + "s " + i.easing, a = {
                            position: "fixed",
                            zIndex: 9999,
                            left: 0,
                            top: 0,
                            "-webkit-backface-visibility": "hidden"
                        }, r = "transition";
                        return a["-webkit-" + r] = a["-moz-" + r] = a["-o-" + r] = a[r] = n, t.css(a), t
                    }, d = function () {
                        t.content.css("visibility", "visible")
                    };
                    _("BuildControls" + n, function () {
                        if (t._allowZoom()) {
                            if (clearTimeout(a), t.content.css("visibility", "hidden"), !(e = t._getItemToZoom()))return void d();
                            (r = c(e)).css(t._getOffset()), t.wrap.append(r), a = setTimeout(function () {
                                r.css(t._getOffset(!0)), a = setTimeout(function () {
                                    d(), setTimeout(function () {
                                        r.remove(), e = r = null, w("ZoomAnimationEnded")
                                    }, 16)
                                }, o)
                            }, 16)
                        }
                    }), _(l + n, function () {
                        if (t._allowZoom()) {
                            if (clearTimeout(a), t.st.removalDelay = o, !e) {
                                if (!(e = t._getItemToZoom()))return;
                                r = c(e)
                            }
                            r.css(t._getOffset(!0)), t.wrap.append(r), t.content.css("visibility", "hidden"), setTimeout(function () {
                                r.css(t._getOffset())
                            }, 16)
                        }
                    }), _(s + n, function () {
                        t._allowZoom() && (d(), r && r.remove(), e = null)
                    })
                }
            }, _allowZoom: function () {
                return "image" === t.currItem.type
            }, _getItemToZoom: function () {
                return !!t.currItem.hasSize && t.currItem.img
            }, _getOffset: function (i) {
                var n, a = (n = i ? t.currItem.img : t.st.zoom.opener(t.currItem.el || t.currItem)).offset(), r = parseInt(n.css("padding-top"), 10), o = parseInt(n.css("padding-bottom"), 10);
                a.top -= e(window).scrollTop() - r;
                var s = {width: n.width(), height: (y ? n.innerHeight() : n[0].offsetHeight) - o - r};
                return M() ? s["-moz-transform"] = s.transform = "translate(" + a.left + "px," + a.top + "px)" : (s.left = a.left, s.top = a.top), s
            }
        }
    });
    var Q = "iframe", O = function (e) {
        if (t.currTemplate[Q]) {
            var i = t.currTemplate[Q].find("iframe");
            i.length && (e || (i[0].src = "//about:blank"), t.isIE8 && i.css("display", e ? "block" : "none"))
        }
    };
    e.magnificPopup.registerModule(Q, {
        options: {
            markup: '<div class="mfp-iframe-scaler"><div class="mfp-close"></div><iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen></iframe></div>',
            srcAction: "iframe_src",
            patterns: {
                youtube: {index: "youtube.com", id: "v=", src: "//www.youtube.com/embed/%id%?autoplay=1"},
                vimeo: {index: "vimeo.com/", id: "/", src: "//player.vimeo.com/video/%id%?autoplay=1"},
                gmaps: {index: "//maps.google.", src: "%id%&output=embed"}
            }
        }, proto: {
            initIframe: function () {
                t.types.push(Q), _("BeforeChange", function (e, t, i) {
                    t !== i && (t === Q ? O() : i === Q && O(!0))
                }), _(s + "." + Q, function () {
                    O()
                })
            }, getIframe: function (i, n) {
                var a = i.src, r = t.st.iframe;
                e.each(r.patterns, function () {
                    return a.indexOf(this.index) > -1 ? (this.id && (a = "string" == typeof this.id ? a.substr(a.lastIndexOf(this.id) + this.id.length, a.length) : this.id.call(this, a)), a = this.src.replace("%id%", a), !1) : void 0
                });
                var o = {};
                return r.srcAction && (o[r.srcAction] = a), t._parseMarkup(n, o, i), t.updateStatus("ready"), n
            }
        }
    });
    var B = function (e) {
        var i = t.items.length;
        return e > i - 1 ? e - i : 0 > e ? i + e : e
    }, H = function (e, t, i) {
        return e.replace(/%curr%/gi, t + 1).replace(/%total%/gi, i)
    };
    e.magnificPopup.registerModule("gallery", {
        options: {
            enabled: !1,
            arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
            preload: [0, 2],
            navigateByImgClick: !0,
            arrows: !0,
            tPrev: "Previous (Left arrow key)",
            tNext: "Next (Right arrow key)",
            tCounter: "%curr% of %total%"
        }, proto: {
            initGallery: function () {
                var i = t.st.gallery, a = ".mfp-gallery";
                return t.direction = !0, !(!i || !i.enabled) && (r += " mfp-gallery", _(d + a, function () {
                    i.navigateByImgClick && t.wrap.on("click" + a, ".mfp-img", function () {
                        return t.items.length > 1 ? (t.next(), !1) : void 0
                    }), n.on("keydown" + a, function (e) {
                        37 === e.keyCode ? t.prev() : 39 === e.keyCode && t.next()
                    })
                }), _("UpdateStatus" + a, function (e, i) {
                    i.text && (i.text = H(i.text, t.currItem.index, t.items.length))
                }), _(c + a, function (e, n, a, r) {
                    var o = t.items.length;
                    a.counter = o > 1 ? H(i.tCounter, r.index, o) : ""
                }), _("BuildControls" + a, function () {
                    if (t.items.length > 1 && i.arrows && !t.arrowLeft) {
                        var n = i.arrowMarkup, a = t.arrowLeft = e(n.replace(/%title%/gi, i.tPrev).replace(/%dir%/gi, "left")).addClass(g), r = t.arrowRight = e(n.replace(/%title%/gi, i.tNext).replace(/%dir%/gi, "right")).addClass(g);
                        a.click(function () {
                            t.prev()
                        }), r.click(function () {
                            t.next()
                        }), t.container.append(a.add(r))
                    }
                }), _(u + a, function () {
                    t._preloadTimeout && clearTimeout(t._preloadTimeout), t._preloadTimeout = setTimeout(function () {
                        t.preloadNearbyImages(), t._preloadTimeout = null
                    }, 16)
                }), void _(s + a, function () {
                    n.off(a), t.wrap.off("click" + a), t.arrowRight = t.arrowLeft = null
                }))
            }, next: function () {
                t.direction = !0, t.index = B(t.index + 1), t.updateItemHTML()
            }, prev: function () {
                t.direction = !1, t.index = B(t.index - 1), t.updateItemHTML()
            }, goTo: function (e) {
                t.direction = e >= t.index, t.index = e, t.updateItemHTML()
            }, preloadNearbyImages: function () {
                var e, i = t.st.gallery.preload, n = Math.min(i[0], t.items.length), a = Math.min(i[1], t.items.length);
                for (e = 1; e <= (t.direction ? a : n); e++)t._preloadItem(t.index + e);
                for (e = 1; e <= (t.direction ? n : a); e++)t._preloadItem(t.index - e)
            }, _preloadItem: function (i) {
                if (i = B(i), !t.items[i].preloaded) {
                    var n = t.items[i];
                    n.parsed || (n = t.parseEl(i)), w("LazyLoad", n), "image" === n.type && (n.img = e('<img class="mfp-img" />').on("load.mfploader", function () {
                        n.hasSize = !0
                    }).on("error.mfploader", function () {
                        n.hasSize = !0, n.loadError = !0, w("LazyLoadError", n)
                    }).attr("src", n.src)), n.preloaded = !0
                }
            }
        }
    });
    var N = "retina";
    e.magnificPopup.registerModule(N, {
        options: {
            replaceSrc: function (e) {
                return e.src.replace(/\.\w+$/, function (e) {
                    return "@2x" + e
                })
            }, ratio: 1
        }, proto: {
            initRetina: function () {
                if (window.devicePixelRatio > 1) {
                    var e = t.st.retina, i = e.ratio;
                    (i = isNaN(i) ? i() : i) > 1 && (_("ImageHasSize." + N, function (e, t) {
                        t.img.css({"max-width": t.img[0].naturalWidth / i, width: "100%"})
                    }), _("ElementParse." + N, function (t, n) {
                        n.src = e.replaceSrc(n, i)
                    }))
                }
            }
        }
    }), k()
}), window.UM_Gallery_Pro = {}, function (e, t, i) {
    Dropzone.autoDiscover = !1;
    var n = "";
    i.init = function () {
        i.current_photo_id = 0, i.events()
    }, i.events = function () {
        jQuery("#um-gallery-comments").comments(), jQuery(document).on("click", ".um-gallery-form,.um-gallery-edit-link", function (e) {
            e.preventDefault();
            var n = t(this).data("id");
            i._um_gallery_album_form(n)
        }), jQuery(document).on("click", ".um-gallery-pro-action-buttons ul li a", function (e) {
            e.preventDefault(), jQuery(".um-gallery-pro-action-buttons ul li").removeClass("active"), jQuery(this).parent("li").addClass("active");
            var n = t(this).attr("href");
            n = n.split("#")[1], i.um_gallery_change_tab(n)
        }), jQuery(document).on("click", ".um-gallery-add-video", function (e) {
            e.preventDefault();
            var n = t("#um-gallery-pro-video-insert #video_url"), a = n.val();
            if (a) {
                var r = i.um_gallery_get_video_type(a);
                if (r.type) {
                    var o = "", s = "", l = "", c = jQuery(".um-gallery-pro-video-list");
                    "youtube" == r.type && (s = r.id, o = "//i.ytimg.com/vi/" + s + "/hqdefault.jpg", l = '<div class="um-gallery-video-items"><div class="um-gallery-video-image"><img src="' + o + '" /></div><input type="hidden" class="um-gallery-video-url" name="video[]" value="' + a + '" />', c.append(l)), "vimeo" == r.type && t.ajax({
                        type: "GET",
                        url: "//vimeo.com/api/v2/video/" + r.id + ".json",
                        jsonp: "callback",
                        dataType: "jsonp",
                        success: function (e) {
                            o = e[0].thumbnail_large, l = '<div class="um-gallery-video-items"><div class="um-gallery-video-image"><img src="' + o + '" /></div><input type="hidden" class="um-gallery-video-url" name="video[]" value="' + a + '" />', c.append(l)
                        }
                    }), n.val("")
                }
            }
        }), jQuery(document).on("click", "#um-gallery-caption-edit,.um-gallery-quick-edit", function (e) {
            e.preventDefault(), t(this).data("id"), t(".um-user-gallery-modify").slideDown(500), t(".um-user-gallery-caption,#um-gallery-caption-edit").slideUp(500)
        });
        var e = !1;
        jQuery(document).on("click", "#um-gallery-save", function (n) {
            if (n.preventDefault(), !e) {
                e = !0;
                var a = t(this), r = a.data("id");
                type = a.data("type"), "album" === type && (a.text("Saving..."), i._um_gallery_album_save(r).promise().done(function () {
                    e = !1, console.log("aaaaaaaa")
                }))
            }
        }), jQuery(document).on("click", ".um-delete-album", function (e) {
            e.preventDefault();
            var n = t(this).data("id");
            i._um_gallery_album_delete(n)
        }), jQuery(document).on("click", ".um-gallery-delete-item", function (e) {
            e.preventDefault();
            var t = jQuery(this).data("id"), i = jQuery(this);
            jQuery.ajax({
                method: "POST",
                url: um_gallery_config.ajax_url,
                data: {action: "sp_gallery_um_delete", id: t, album_id: um_gallery_config.album_id},
                success: function () {
                    i.closest(".um-gallery-item").slideUp().remove(), PubSub.publish("refresh_iLightBox", "iLightBox is refreshed")
                }
            })
        }), jQuery(document).on("click", ".um-gallery-close,.um-gallery-cancel", function (e) {
            e.preventDefault(), jQuery.magnificPopup.close()
        }), jQuery(document).on("click", "#savePhoto", function (e) {
            e.preventDefault();
            var n = t("#um-gallery-modal").data("id");
            i._um_gallery_edit_photo(n)
        }), jQuery(document).on("click", "#cancelPhoto", function (e) {
            e.preventDefault(), t(".um-user-gallery-modify").slideUp(500), t(".um-user-gallery-caption,#um-gallery-caption-edit").slideDown(500)
        }), jQuery(document).on("click", ".um-gallery-open-photo", function (e) {
            e.preventDefault();
            var t = jQuery(this).data("id");
            i._um_gallery_open_photo(t)
        }), jQuery(document).on("click", ".aqm-delete-gallery-photo", function (e) {
            e.preventDefault(), jQuery(".um-user-gallery-normal").slideUp(500), jQuery(".um-user-gallery-edit").slideDown(600)
        }), t(document).on("click", ".um-user-gallery-confirm", function (e) {
            e.preventDefault();
            var n = t(this).data("option");
            if ("no" === n)t(".um-user-gallery-normal").slideDown(500), t(".um-user-gallery-edit").slideUp(600); else if ("yes" === n) {
                var a = t("#um-gallery-modal").data("id");
                i._um_gallery_photo_delete(a)
            }
        }), jQuery(document).on("click", ".um-user-gallery-arrow a", function (e) {
            e.preventDefault();
            var n, a, r = jQuery("#um-gallery-modal").data("id"), o = t(this).data("direction"), s = "", l = [];
            jQuery.each(um_gallery_images, function (e) {
                l.push(e)
            }), jQuery.each(l, function (e, t) {
                t == r && (a = e > 0 ? l[e - 1] : l[l.length - 1], n = e + 1 < l.length ? l[e + 1] : l[0])
            }), "left" === o && (s = n, i._um_load_image(s)), "right" === o && (s = a, i._um_load_image(s))
        }), jQuery(document).on("keydown", function (e) {
            if (t(".mfp-wrap #um-gallery-modal").length && "input" !== e.target.tagName.toLowerCase() && "textarea" !== e.target.tagName.toLowerCase()) {
                var n = jQuery("#um-gallery-modal").data("id");
                37 == e.keyCode ? (adjacent_id = jQuery("#um-gallery-item-" + n).closest(".um-gallery-item").prev().find(".um-gallery-open-photo").data("id"), i._um_load_image(adjacent_id)) : 39 == e.keyCode && (adjacent_id = jQuery("#um-gallery-item-" + n).closest(".um-gallery-item").next().find(".um-gallery-open-photo").data("id"), i._um_load_image(adjacent_id))
            }
        })
    }, i._um_gallery_album_save = function (e) {
        var t = jQuery("#album_name").val(), a = jQuery("#album_description").val(), r = jQuery("#album_privacy").val(), o = !1;
        return n.files.length > 0 && (o = !0), jQuery(".um-gallery-message").html("").slideUp(), jQuery.ajax({
            type: "post",
            url: um_gallery_config.ajax_url,
            data: {
                action: "um_gallery_album_update",
                id: e,
                album_name: t,
                album_description: a,
                album_privacy: r,
                security: um_gallery_config.nonce
            },
            cache: !1,
            success: function (e) {
                var t;
                if (e.id) {
                    if (jQuery("#um-gallery-save").data("id", e.id), jQuery(".um-gallery-video-items input").length) {
                        var a = [];
                        jQuery(".um-gallery-video-items input").each(function () {
                            a.push(jQuery(this).val())
                        }), jQuery.ajax({
                            type: "post",
                            url: um_gallery_config.ajax_url,
                            data: {
                                action: "um_gallery_add_videos",
                                album_id: e.id,
                                videos: a,
                                security: um_gallery_config.nonce
                            },
                            cache: !1,
                            success: function (e) {
                                if (jQuery(".um-gallery-video-items input").remove(), !0 === e.success) {
                                    "undefined" != typeof um_gallery_images && (um_gallery_images = e.data.gallery_images);
                                    var t = i.get_video_thumbnail(e.data.video_url);
                                    file_html = "", file_html = '<div class="um-gallery-item um-gallery-col-1-4" id="um-photo-' + e.data.id + '"><div class="um-gallery-inner"><a href="' + e.data.video_url + '" data-ns-rel="iLightbox[gallery-1]" id="um-gallery-item-' + e.data.id + '" data-caption="' + e.image_caption + '"  data-id="' + e.data.id + '"><img src="' + t + '" /></a><div class="ns-gallery-actions"><a href="#" class="um-gallery-delete-item" data-id="' + e.id + '"><i class="um-faicon-trash"></i></a><a href="#" class="ns_um-gallery-caption-edit" data-id="' + e.id + '"><i class="um-faicon-pencil"></i> <span>Edit Caption</span></a><div class="ns-edit-wrapper"><input type="text" class="ns-caption" value="' + e.image_caption + '"></div></div></div></div>', jQuery(".um-gallery-grid").append(file_html)
                                }
                            }
                        })
                    }
                    1 == o ? (n.on("sending", function (t, i, n) {
                        n.append("album_id", e.id), n.append("action", "um_gallery_photo_upload")
                    }), n.processQueue(), n.on("complete", function (n) {
                        t = n.xhr.response, t = jQuery.parseJSON(t), ns_json_obj = {} , file_html = "",  file_html = '<div class="um-gallery-item um-gallery-col-1-4" id="um-photo-' + t.id + '" data-ns-sort="\{ &quot;image_id&quot;: '+t.id+' , &quot;user_id&quot;: '+um_gallery_config.user.id+' , &quot;menu_order&quot;: 1 \} "> <div class="um-gallery-inner"><a href="' + t.image_src + '" data-ns-rel="iLightbox[gallery-1]" id="um-gallery-item-' + t.id + '" data-caption="' + t.image_caption + '" data-id="' + t.id + '"><img src="' + t.thumb + '" /></a><div class="ns-gallery-actions"><a href="#" class="um-gallery-delete-item" data-id="' + t.id + '"><i class="um-faicon-trash"></i></a><a href="#" class="ns_um-gallery-caption-edit" data-id="' + t.id + '"><i class="um-faicon-pencil"></i> <span>Edit Caption</span></a><div class="ns-edit-wrapper"><input type="text" class="ns-caption" value="' + t.image_caption + '"></div></div></div></div>',  jQuery(".um-gallery-grid").append(file_html), "undefined" != typeof um_gallery_images && (um_gallery_images = t.gallery_images), !0 === e.new && i._um_gallery_get_album_item(e.id), PubSub.publish("refresh_iLightBox", "iLightBox is refreshed"),jQuery( "[data-is-sortable]" ).sortable('refresh')
                    })) : !0 === e.new && i._um_gallery_get_album_item(e.id)
                }
            }
        })
    }, i._um_gallery_get_album_item = function (e) {
        return !jQuery("#um-album-" + e).length && void jQuery.ajax({
                type: "get",
                url: um_gallery_config.ajax_url,
                data: {action: "um_gallery_get_album_item", album_id: e, security: um_gallery_config.nonce},
                cache: !1,
                success: function (e) {
                    jQuery(".um-gallery-album-list").append(e), PubSub.publish("refresh_iLightBox", "iLightBox is refreshed")
                }
            })
    }, i._um_gallery_edit_photo = function (e) {
        var t = jQuery(".um-user-gallery-modify #caption").val(), n = jQuery(".um-user-gallery-modify #description").val(), a = um_gallery_images[e].caption;
        jQuery.ajax({
            type: "post",
            url: um_gallery_config.ajax_url,
            data: {
                action: "um_gallery_photo_update",
                id: e,
                album_id: um_gallery_config.album_id,
                caption: t,
                default_caption: a,
                description: n,
                security: um_gallery_config.nonce
            },
            cache: !1,
            success: function (t) {
                um_gallery_images = t, i._um_load_image(e)
            }
        })
    }, i._um_gallery_enable_edit = function () {
    }, i._um_gallery_album_form = function (e) {
        var t = "#um-gallery-modal";
        jQuery(t).html('<div class="um-gallery-loader"><i class="fa fa-spin fa-spinner"></i></div>'), jQuery.magnificPopup.open({
            items: {src: jQuery('<div id="um-gallery-modal" class="um-gallery-popup"></div>')},
            closeMarkup: '<a title="%title%" class="mfp-close">&#215;</a>',
            type: "inline",
            mainClass: "um-gallery-modal-wrapper"
        }, 0), e || (e = 0), jQuery.ajax({
            type: "get",
            url: um_gallery_config.ajax_url,
            data: {action: "um_gallery_get_album_form", album_id: e},
            success: function (e) {
                jQuery(t).html(e), jQuery(t).animate({"max-width": "740px"}, "slow"), n = new Dropzone("#dropzone", {
                    url: um_gallery_config.ajax_url,
                    autoProcessQueue: !1,
                    parallelUploads: 56,
                    method: "post",
                    acceptedFiles: "image/*",
                    dictDefaultMessage: um_gallery_config.dictDefaultMessage,
                    queuecomplete: function () {
                    },
                    success: function () {
                        jQuery(".um-gallery-message").html(um_gallery_config.upload_complete).slideDown(), jQuery("#um-gallery-save").text("Save"), setTimeout(function () {
                            jQuery("#um-gallery-cancel").trigger("click")
                        }, 1e3)
                    }
                })
            }
        })
    }, i._um_gallery_photo_delete = function (e) {
        jQuery.ajax({
            type: "post",
            url: um_gallery_config.ajax_url,
            data: {
                action: "sp_gallery_um_delete",
                id: e,
                album_id: um_gallery_config.album_id,
                security: um_gallery_config.nonce
            },
            cache: !1,
            success: function (t) {
                jQuery.magnificPopup.close(), jQuery("#um-photo-" + e).slideUp().remove(), um_gallery_images = t, PubSub.publish("refresh_iLightBox", "iLightBox is refreshed")
            }
        })
    }, i._um_gallery_album_delete = function (e) {
        jQuery.ajax({
            type: "post",
            url: um_gallery_config.ajax_url,
            data: {action: "um_gallery_delete_album", id: e, security: um_gallery_config.nonce},
            cache: !1,
            success: function () {
                jQuery("#um-album-" + e).slideUp().remove()
            }
        })
    }, i._um_load_info = function (e) {
        jQuery.ajax({
            type: "get",
            url: um_gallery_config.ajax_url,
            data: {action: "um_photo_info", id: e, security: um_gallery_config.nonce},
            cache: !1,
            success: function (e) {
                jQuery("#um-user-gallery-title").text(e.title), jQuery("#um-user-gallery-description").text(e.caption)
            }
        })
    }, i._um_load_image = function (e) {
        if (!e || "undefined" == e)return !1;
        i.current_photo_id = e;
        var t = jQuery(".image-holder");
        t.html("");
        var n = um_gallery_images[e].type, a = jQuery("#um-gallery-item-" + e).attr("data-source-url");
        if ("youtube" == n || "vimeo" == n) {
            if ("youtube" == n) {
                r = i.um_gallery_get_video_type(a);
                video_id = r.id, t.html('<iframe class="mfp-iframe" width="100%" src="//www.youtube.com/embed/' + video_id + '" frameborder="0" allowfullscreen></iframe>')
            } else if ("vimeo" == n) {
                var r = i.um_gallery_get_video_type(a);
                video_id = r.id, t.html('<iframe src="//player.vimeo.com/video/' + video_id + '" width="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>')
            }
            jQuery(".um-user-gallery-image-wrap").css("background-image", "none")
        } else jQuery(".um-user-gallery-image-wrap").css("background-image", "url(" + a + ")");
        jQuery("#um-gallery-modal,#um-gallery-caption-edit").data("id", e), jQuery("#aqm_comment_id").val(e);
        var o = um_gallery_images[e].user_id, s = '<div class="um-gallery-header-avatar"><a href="' + um_gallery_users[o].link + '">' + um_gallery_users[o].avatar + '</a></div><div class=""><a href="' + um_gallery_users[o].link + '">' + um_gallery_users[o].name + "</a></div>";
        jQuery(".um-user-gallery-user").html(s);
        var l = um_gallery_images[e].caption, c = um_gallery_images[e].description;
        jQuery("#um-user-gallery-title").html(l), jQuery(".um-user-gallery-modify #caption").val(l), jQuery("#um-user-gallery-description").html(c), jQuery(".um-user-gallery-modify #description").val(c), jQuery(".um-user-gallery-modify").hide(), jQuery(".um-user-gallery-caption,#um-gallery-caption-edit").show(), um_gallery_config.enable_comments && i.fetchComments(e)
    }, i.fetchComments = function () {
        t("#um-gallery-comments").comments({
            enableReplying: !0,
            currentUserId: !!um_gallery_config.user && um_gallery_config.user.id,
            readOnly: !um_gallery_config.user,
            roundProfilePictures: !0,
            enableDeletingCommentWithReplies: !0,
            enableNavigation: !1,
            enableUpvoting: !1,
            profilePictureURL: um_gallery_config.user && um_gallery_config.user.avatar ? um_gallery_config.user.avatar : "",
            getComments: function (e, n) {
                t.ajax({
                    type: "get",
                    url: um_gallery_config.ajax_url,
                    data: {action: "um_gallery_get_comments", id: i.current_photo_id},
                    success: function (t) {
                        e(t)
                    },
                    error: n
                })
            },
            postComment: function (e, n, a) {
                e.action = "um_gallery_post_comment", e.photo_id = i.current_photo_id, t.ajax({
                    type: "post",
                    url: um_gallery_config.ajax_url,
                    data: e,
                    success: function (t) {
                        e.id = t.id, n(e)
                    },
                    error: a
                })
            },
            putComment: function (e, n, a) {
                e.action = "um_gallery_post_comment", e.photo_id = i.current_photo_id, t.ajax({
                    type: "post",
                    url: um_gallery_config.ajax_url,
                    data: e,
                    success: function () {
                        n(e)
                    },
                    error: a
                })
            },
            deleteComment: function (e, i, n) {
                e.action = "um_gallery_delete_comment", t.ajax({
                    type: "post",
                    url: um_gallery_config.ajax_url,
                    data: e,
                    success: i,
                    error: n
                })
            }
        })
    }, i._um_gallery_open_photo = function (t) {
        var n = jQuery("#um-gallery-item-" + t).attr("href"), a = '<div id="um-gallery-modal" class="um-gallery-popup" data-id="' + t + '"><div class="um-user-gallery-inner"><div class="um-user-gallery-left"><div class="um-user-gallery-arrow aqm-left-gallery-arrow"><a href="#" data-direction="left"><i class="um-faicon-angle-left" aria-hidden="true"></i></a></div><div class="um-user-gallery-arrow aqm-right-gallery-arrow"><a href="#" data-direction="right"><i class="um-faicon-angle-right" aria-hidden="true"></i></a></div><div class="um-user-gallery-image-wrap"><div class="image-holder"></div></div><div class="um-user-gallery-image-options"><span class="um-user-gallery-options">';
        um_gallery_config.is_owner && (a += '<div class="um-user-gallery-normal"><a href="#" class="um-gallery-quick-edit">Edit Photo</a> | <a href="#" class="aqm-delete-gallery-photo">Delete Photo</a></div><div class="um-user-gallery-edit" style="display: none;">Are you sure want to delete this? <a href="#"  class="um-user-gallery-confirm" data-option="yes">Yes</a> | <a href="#" class="um-user-gallery-confirm" data-option="no">No</a></div>'), a += '</span></div></div><div class="um-user-gallery-right"><div class="um-user-gallery-right-inner"><div class="um-user-gallery-user"></div><div class="um-user-gallery-info"><div class="um-user-gallery-caption"><div class="um-user-gallery-title" id="um-user-gallery-title"></div><div class="um-user-gallery-description" id="um-user-gallery-description"></div></div>', um_gallery_config.is_owner && (a += '<div class="um-user-gallery-modify"><div class="um-caption-text"><input type="hidden" id="caption" /></div><div class="um-caption-text"><textarea id="description" /></div><div class="um-caption-text"><input type="submit" id="savePhoto" value="' + um_gallery_config.save_text + '" /><input type="button" id="cancelPhoto" value="' + um_gallery_config.cancel_text + '" /></div></div><div class="um-gallery-caption-edit-wrapper"><a href="#" id="um-gallery-caption-edit" data-id="">' + um_gallery_config.edit_text + "</a></div>"), a += "</div>", um_gallery_config.enable_comments && (a += '<div id="um-gallery-comments"></div>'), a += "</div></div></div></div>", jQuery.magnificPopup.open({
            items: {src: jQuery(a)},
            type: "inline",
            closeMarkup: '<a title="%title%" class="mfp-close">&#215;</a>',
            mainClass: "um-gallery-modal-wrapper",
            callbacks: {
                open: function () {
                    jQuery("body").addClass("gallery-open");
                    var a = jQuery(e).width(), r = a - .15 * a;
                    r = Math.round(r), jQuery("#um-gallery-modal").animate({"max-width": r + "px"}, "slow"), jQuery(".um-user-gallery-image-wrap").css("background-image", "url(" + n + ")"), i._um_load_image(t)
                }, close: function () {
                    jQuery("body").removeClass("gallery-open")
                }
            }
        }, 0)
    }, i._um_gallery_comment_height = function () {
        var e = 100;
        return void jQuery(".cmmnt-content p").each(function () {
            var t = jQuery(this).html();
            if (t.length > e) {
                var i = t.substr(0, e) + '<span class="moreellipses">...&nbsp;</span><span class="morecontent"><span>' + t.substr(99, t.length - e) + '</span>&nbsp;&nbsp;<a href="" class="morelink">more</a></span>';
                jQuery(this).html(i)
            }
        })
    }, i.um_gallery_change_tab = function (e) {
        "" == e && (e = "photo"), jQuery(".um-gallery-form-tabs > div").hide(), jQuery("#um-gallery-form-tab-" + e).show()
    }, i.get_video_thumbnail = function (e) {
        var n = i.um_gallery_get_video_type(e);
        if (n.type) {
            var a = "", r = "";
            return jQuery(".um-gallery-pro-video-list"), "youtube" == n.type && (r = n.id, a = "//i.ytimg.com/vi/" + r + "/0.jpg"), "vimeo" == n.type && t.ajax({
                type: "GET",
                url: "//vimeo.com/api/v2/video/" + n.id + ".json",
                jsonp: "callback",
                dataType: "jsonp",
                success: function (e) {
                    a = e[0].thumbnail_large
                }
            }), a
        }
    }, i.um_gallery_get_video_type = function (e) {
        if ("" != e) {
            if (e.match(/(http:\/\/|https:\/\/|)(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/), RegExp.$3.indexOf("youtu") > -1)t = "youtube"; else if (RegExp.$3.indexOf("vimeo") > -1)var t = "vimeo";
            return {type: t, id: RegExp.$6}
        }
    }, t(i.init)
}(window, jQuery, window.UM_Gallery_Pro), jQuery(window).resize(function () {
    var e = jQuery(window).width(), t = e - .15 * e;
    t = Math.round(t)
});