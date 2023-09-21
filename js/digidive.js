/*
* -----------------------------------------------------------------------------
* digidive JS
* Version: Dev
* Copyright, Julia Koblitz
* Licensed under MIT
* -----------------------------------------------------------------------------
* The above notice must be included in its entirety when this file is used.
*/

// Namespaced function to check whether a selector is supported by the browser or not
// Original is by Diego Perini, lifted from https://gist.github.com/paulirish/441842
function digidive_selectorSupported(selector) {
    var support, link, sheet, doc = document,
        root = doc.documentElement,
        head = root.getElementsByTagName("head")[0],

        impl = doc.implementation || {
            hasFeature: function () {
                return false;
            }
        },

        link = doc.createElement("style");
    link.type = "text/css";

    (head || root).insertBefore(link, (head || root).firstChild);

    sheet = link.sheet || link.styleSheet;

    if (!(sheet && selector)) return false;

    support = impl.hasFeature("CSS2", "") ?
        function (selector) {
            try {
                sheet.insertRule(selector + "{ }", 0);
                sheet.deleteRule(sheet.cssRules.length - 1);
            } catch (e) {
                return false;
            }
            return true;
        } : function (selector) {
            sheet.cssText = selector + " { }";
            return sheet.cssText.length !== 0 && !(/unknown/i).test(sheet.cssText) && sheet.cssText.indexOf(selector) === 0;
        };

    return support(selector);
};


/* Start polyfills */

// Polyfill for Element.matches()
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

// Polyfill for Element.closest()
if (!Element.prototype.closest) {
    Element.prototype.closest = function (s) {
        var el = this;
        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

// Polyfill for Element.classList (http://purl.eligrey.com/github/classList.js/blob/master/classList.js)
"document" in self && ("classList" in document.createElement("_") && (!document.createElementNS || "classList" in document.createElementNS("http://www.w3.org/2000/svg", "g")) || !function (t) { "use strict"; if ("Element" in t) { var e = "classList", n = "prototype", i = t.Element[n], s = Object, r = String[n].trim || function () { return this.replace(/^\s+|\s+$/g, "") }, o = Array[n].indexOf || function (t) { for (var e = 0, n = this.length; n > e; e++)if (e in this && this[e] === t) return e; return -1 }, c = function (t, e) { this.name = t, this.code = DOMException[t], this.message = e }, a = function (t, e) { if ("" === e) throw new c("SYNTAX_ERR", "The token must not be empty."); if (/\s/.test(e)) throw new c("INVALID_CHARACTER_ERR", "The token must not contain space characters."); return o.call(t, e) }, l = function (t) { for (var e = r.call(t.getAttribute("class") || ""), n = e ? e.split(/\s+/) : [], i = 0, s = n.length; s > i; i++)this.push(n[i]); this._updateClassName = function () { t.setAttribute("class", this.toString()) } }, u = l[n] = [], h = function () { return new l(this) }; if (c[n] = Error[n], u.item = function (t) { return this[t] || null }, u.contains = function (t) { return ~a(this, t + "") }, u.add = function () { var t, e = arguments, n = 0, i = e.length, s = !1; do t = e[n] + "", ~a(this, t) || (this.push(t), s = !0); while (++n < i); s && this._updateClassName() }, u.remove = function () { var t, e, n = arguments, i = 0, s = n.length, r = !1; do for (t = n[i] + "", e = a(this, t); ~e;)this.splice(e, 1), r = !0, e = a(this, t); while (++i < s); r && this._updateClassName() }, u.toggle = function (t, e) { var n = this.contains(t), i = n ? e !== !0 && "remove" : e !== !1 && "add"; return i && this[i](t), e === !0 || e === !1 ? e : !n }, u.replace = function (t, e) { var n = a(t + ""); ~n && (this.splice(n, 1, e), this._updateClassName()) }, u.toString = function () { return this.join(" ") }, s.defineProperty) { var f = { get: h, enumerable: !0, configurable: !0 }; try { s.defineProperty(i, e, f) } catch (p) { void 0 !== p.number && -2146823252 !== p.number || (f.enumerable = !1, s.defineProperty(i, e, f)) } } else s[n].__defineGetter__ && i.__defineGetter__(e, h) } }(self), function () { "use strict"; var t = document.createElement("_"); if (t.classList.add("c1", "c2"), !t.classList.contains("c2")) { var e = function (t) { var e = DOMTokenList.prototype[t]; DOMTokenList.prototype[t] = function (t) { var n, i = arguments.length; for (n = 0; i > n; n++)t = arguments[n], e.call(this, t) } }; e("add"), e("remove") } if (t.classList.toggle("c3", !1), t.classList.contains("c3")) { var n = DOMTokenList.prototype.toggle; DOMTokenList.prototype.toggle = function (t, e) { return 1 in arguments && !this.contains(t) == !e ? e : n.call(this, t) } } "replace" in document.createElement("_").classList || (DOMTokenList.prototype.replace = function (t, e) { var n = this.toString().split(" "), i = n.indexOf(t + ""); ~i && (n = n.slice(i), this.remove.apply(this, n), this.add(e), this.add.apply(this, n.slice(1))) }), t = null }());

// Polyfill for :focus-visible (https://github.com/WICG/focus-visible)
// Only applied if the selector is not supported by the browser natively
if (!digidive_selectorSupported(":focus-visible")) {
    !function (e, t) { "object" == typeof exports && "undefined" != typeof module ? t() : "function" == typeof define && define.amd ? define(t) : t() }(0, function () { "use strict"; function e(e) { var t = !0, n = !1, o = null, d = { text: !0, search: !0, url: !0, tel: !0, email: !0, password: !0, number: !0, date: !0, month: !0, week: !0, time: !0, datetime: !0, "datetime-local": !0 }; function i(e) { return !!(e && e !== document && "HTML" !== e.nodeName && "BODY" !== e.nodeName && "classList" in e && "contains" in e.classList) } function s(e) { e.classList.contains("focus-visible") || (e.classList.add("focus-visible"), e.setAttribute("data-focus-visible-added", "")) } function u(e) { t = !1 } function a() { document.addEventListener("mousemove", c), document.addEventListener("mousedown", c), document.addEventListener("mouseup", c), document.addEventListener("pointermove", c), document.addEventListener("pointerdown", c), document.addEventListener("pointerup", c), document.addEventListener("touchmove", c), document.addEventListener("touchstart", c), document.addEventListener("touchend", c) } function c(e) { e.target.nodeName && "html" === e.target.nodeName.toLowerCase() || (t = !1, document.removeEventListener("mousemove", c), document.removeEventListener("mousedown", c), document.removeEventListener("mouseup", c), document.removeEventListener("pointermove", c), document.removeEventListener("pointerdown", c), document.removeEventListener("pointerup", c), document.removeEventListener("touchmove", c), document.removeEventListener("touchstart", c), document.removeEventListener("touchend", c)) } document.addEventListener("keydown", function (n) { n.metaKey || n.altKey || n.ctrlKey || (i(e.activeElement) && s(e.activeElement), t = !0) }, !0), document.addEventListener("mousedown", u, !0), document.addEventListener("pointerdown", u, !0), document.addEventListener("touchstart", u, !0), document.addEventListener("visibilitychange", function (e) { "hidden" === document.visibilityState && (n && (t = !0), a()) }, !0), a(), e.addEventListener("focus", function (e) { var n, o, u; i(e.target) && (t || (n = e.target, o = n.type, "INPUT" === (u = n.tagName) && d[o] && !n.readOnly || "TEXTAREA" === u && !n.readOnly || n.isContentEditable)) && s(e.target) }, !0), e.addEventListener("blur", function (e) { var t; i(e.target) && (e.target.classList.contains("focus-visible") || e.target.hasAttribute("data-focus-visible-added")) && (n = !0, window.clearTimeout(o), o = window.setTimeout(function () { n = !1 }, 100), (t = e.target).hasAttribute("data-focus-visible-added") && (t.classList.remove("focus-visible"), t.removeAttribute("data-focus-visible-added"))) }, !0), e.nodeType === Node.DOCUMENT_FRAGMENT_NODE && e.host ? e.host.setAttribute("data-js-focus-visible", "") : e.nodeType === Node.DOCUMENT_NODE && (document.documentElement.classList.add("js-focus-visible"), document.documentElement.setAttribute("data-js-focus-visible", "")) } if ("undefined" != typeof window && "undefined" != typeof document) { var t; window.applyFocusVisiblePolyfill = e; try { t = new CustomEvent("focus-visible-polyfill-ready") } catch (e) { (t = document.createEvent("CustomEvent")).initCustomEvent("focus-visible-polyfill-ready", !1, !1, {}) } window.dispatchEvent(t) } "undefined" != typeof document && e(document) });
}

/* End polyfills */


/* digidive JS core */

var digidive = {
    // Getting the required elements
    // Re-initialized once the DOM is loaded (to avoid issues with virtual DOM)
    pageWrapper: document.getElementsByClassName("page-wrapper")[0],
    stickyAlerts: document.getElementsByClassName("sticky-alerts")[0],

    darkModeOn: false, // Also re-initialized once the DOM is loaded (see below)

    // Create cookie
    createCookie: function (name, value, days) {
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }
        else {
            expires = "";
        }
        document.cookie = name + "=" + value + expires + "; path=/; SameSite=Strict";
    },

    // Read cookie
    readCookie: function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(";");
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === " ") {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    },

    // Erase cookie
    eraseCookie: function (name) {
        digidive.createCookie(name, "", -1);
    },

    // Toggle light/dark mode 
    toggleDarkMode: function () {
        if (document.body.classList.contains("dark-mode")) {
            document.body.classList.remove("dark-mode");
            digidive.darkModeOn = false;
            digidive.createCookie("digidive_preferredMode", "light-mode", 365);
        } else {
            document.body.classList.add("dark-mode");
            digidive.darkModeOn = true;
            digidive.createCookie("digidive_preferredMode", "dark-mode", 365);
        }
    },

    // Get preferred mode
    getPreferredMode: function () {
        if (digidive.readCookie("digidive_preferredMode")) {
            return digidive.readCookie("digidive_preferredMode");
        } else {
            return "not-set";
        }
    },

    // Toggles sidebar
    toggleSidebar: function (btn = null) {
        if (digidive.pageWrapper) {
            if (btn) btn.classList.add('active');
            if (digidive.pageWrapper.getAttribute("data-sidebar-hidden")) {
                digidive.pageWrapper.removeAttribute("data-sidebar-hidden");
                digidive.pageWrapper.removeAttribute("data-sidebar-first");

            } else {
                if (btn) btn.classList.remove('active');
                digidive.pageWrapper.setAttribute("data-sidebar-hidden", "hidden");
            }
        }
    },

    // Deactivate all the dropdown toggles when another one is active
    deactivateAllDropdownToggles: function () {
        var activeDropdownToggles = document.querySelectorAll("[data-toggle='dropdown'].active");
        for (var i = 0; i < activeDropdownToggles.length; i++) {
            activeDropdownToggles[i].classList.remove("active");
            activeDropdownToggles[i].closest(".dropdown").classList.remove("show");
        }
    },

    // Toggle modal (using Javascript)
    toggleModal: function (modalId) {
        var modal = document.getElementById(modalId);

        if (modal) {
            modal.classList.toggle("show");
        }
    },

    // Toggle loader
    toggleLoader: function () {
        var loader = document.querySelector('.loader');

        if (loader) {
            loader.classList.toggle("show");
        }
    },

    /* Code block for handling sticky alerts */

    // Make an ID for an element
    makeId: function (length) {
        var result = "";
        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    },

    // Toast an alert (show, fade, dispose)
    toastAlert: function (alertId, timeShown) {
        var alertElement = document.getElementById(alertId);

        // Setting the default timeShown
        if (timeShown === undefined) {
            timeShown = 5000;
        }

        // Alert is only toasted if it does not have the .show class
        if (!alertElement.classList.contains("show")) {
            // Add .block class if it does not exist
            if (!alertElement.classList.contains("block")) {
                alertElement.classList.add("block");
            }

            // Show the alert
            // The 0.25 seconds delay is for the animation
            setTimeout(function () {
                alertElement.classList.add("show");
            }, 250);

            // Wait some time (timeShown + 250) and fade out the alert
            var timeToFade = timeShown + 250;
            setTimeout(function () {
                alertElement.classList.add("fade");
            }, timeToFade);

            // Wait some more time (timeToFade + 500) and dispose the alert (by removing the .block class)
            // Again, the extra delay is for the animation
            // Remove the .show and .fade classes (so the alert can be toasted again)
            var timeToDestroy = timeToFade + 500;
            setTimeout(function () {
                alertElement.classList.remove("block");
                alertElement.classList.remove("show");
                alertElement.classList.remove("fade");
            }, timeToDestroy);
        }
    },

    // Create a sticky alert, display it, and then remove it
    initStickyAlert: function (param) {
        // Setting the variables from the param
        var content = ("content" in param) ? param.content : "";
        var title = ("title" in param) ? param.title : "";
        var alertType = ("alertType" in param) ? param.alertType : "";
        var fillType = ("filled" in param) ? param.filled : false;
        var hasDismissButton = ("hasDismissButton" in param) ? param.hasDismissButton : true;
        var timeShown = ("timeShown" in param) ? param.timeShown : 5000;

        // Create the alert element
        var alertElement = document.createElement("div");

        // Set ID to the alert element
        alertElement.setAttribute("id", digidive.makeId(6));

        // Add the title
        if (title) {
            content = "<h4 class='title'>" + title + "</h4>" + content;
        }

        // Add the classes to the alert element
        alertElement.classList.add("alert");
        if (alertType) {
            alertElement.classList.add(alertType);
        }
        if (fillType) {
            alertElement.classList.add('filled');
        }

        // Add the close button to the content (if required)
        if (hasDismissButton) {
            content = "<button class='close' data-dismiss='alert' type='button' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + content;
        }

        // Add the content to the alert element
        alertElement.innerHTML = content;

        // Append the alert element to the sticky alerts
        if (digidive.stickyAlerts === undefined) {
            digidive.stickyAlerts = document.getElementsByClassName("sticky-alerts")[0]
        }
        digidive.stickyAlerts.insertBefore(alertElement, digidive.stickyAlerts.childNodes[0]);

        // Toast the alert
        digidive.toastAlert(alertElement.getAttribute("id"), timeShown);
    },

    /* End code block for handling sticky alerts */

    // Click handler that can be overridden by users if needed
    clickHandler: function (event) { },

    // Keydown handler that can be overridden by users if needed
    keydownHandler: function (event) { },

    // Function for binding the input value
    bindInputValue: function (inputElement) {
        if (inputElement.getAttribute("data-target")) {
            var targetElementIDs = inputElement.getAttribute("data-target").replace(/\s+/g, "").split(",");
            var targetElement;

            for (var i = 0; i < targetElementIDs.length; i++) {
                targetElement = document.getElementById(targetElementIDs[i]);
                if (targetElement) {
                    if (targetElement instanceof HTMLInputElement) {
                        targetElement.value = inputElement.value;
                    } else {
                        targetElement.innerText = inputElement.value;
                    }
                }
            }
        }
    },

    // For attaching the bind input value function (meant to be called when an event listener is attached)
    callBindInputValueForAttachment: function (event) {
        digidive.bindInputValue(event.target);
    },
}


/* Things done once the DOM is loaded */

function digidiveOnDOMContentLoaded() {
    // Re-initializing the required elements (to avoid issues with virtual DOM)
    if (!digidive.pageWrapper) {
        digidive.pageWrapper = document.getElementsByClassName("page-wrapper")[0];
    }
    if (!digidive.stickyAlerts) {
        digidive.stickyAlerts = document.getElementsByClassName("sticky-alerts")[0];
    }

    // Hiding sidebar on first load on small screens 
    // add data-sidebar-first as indicator to hide transitions
    if (document.documentElement.clientWidth <= 992) {
        if (digidive.pageWrapper) {
            digidive.pageWrapper.setAttribute("data-sidebar-hidden", "hidden");
            digidive.pageWrapper.setAttribute("data-sidebar-first", "first");
        }
    } 
    console.log(document.documentElement.clientWidth);

    // Adding the click event listener
    document.addEventListener(
        "click",
        function (event) {
            var eventCopy = event;
            var target = event.target;

            // Handle clicks on dropdown toggles
            if (target.matches("[data-toggle='dropdown']") || target.matches("[data-toggle='dropdown'] *")) {
                if (target.matches("[data-toggle='dropdown'] *")) {
                    target = target.closest("[data-toggle='dropdown']");
                }
                if (target.classList.contains("active")) {
                    target.classList.remove("active");
                    target.closest(".dropdown").classList.remove("show");
                } else {
                    digidive.deactivateAllDropdownToggles();
                    target.classList.add("active");
                    target.closest(".dropdown").classList.add("show");
                }
            } else {
                if (!target.matches(".dropdown-menu *")) {
                    digidive.deactivateAllDropdownToggles();
                }
            }

            // Handle clicks on alert dismiss buttons
            if (target.matches(".alert [data-dismiss='alert']") || target.matches(".alert [data-dismiss='alert'] *")) {
                if (target.matches(".alert [data-dismiss='alert'] *")) {
                    target = target.closest(".alert [data-dismiss='alert']");
                }
                target.parentNode.classList.add("dispose");
            }

            // Handle clicks on modal toggles
            if (target.matches("[data-toggle='modal']") || target.matches("[data-toggle='modal'] *")) {
                if (target.matches("[data-toggle='modal'] *")) {
                    target = target.closest("[data-toggle='modal']");
                }
                var targetModal = document.getElementById(target.getAttribute("data-target"));
                if (targetModal) {
                    if (targetModal.classList.contains("modal")) {
                        digidive.toggleModal(target.getAttribute("data-target"));
                    }
                }
            }

            // Handle clicks on modal dismiss buttons
            if (target.matches(".modal [data-dismiss='modal']") || target.matches(".modal [data-dismiss='modal'] *")) {
                if (target.matches(".modal [data-dismiss='modal'] *")) {
                    target = target.closest(".modal [data-dismiss='modal']");
                }
                target.closest(".modal").classList.remove("show");
            }

            // Handle clicks on modal overlays
            if (target.matches(".modal-dialog")) {
                var parentModal = target.closest(".modal");

                if (!parentModal.getAttribute("data-overlay-dismissal-disabled")) {
                    if (parentModal.classList.contains("show")) {
                        parentModal.classList.remove("show");
                    } else {
                        window.location.hash = "#";
                    }
                }
            }

            // Handle clicks on password show/hide toggles
            if (target.matches("[data-toggle='password']") || target.matches("[data-toggle='password'] *")) {
                if (target.matches("[data-toggle='password'] *")) {
                    target = target.closest("[data-toggle='password']");
                }
                var targetInput = document.getElementById(target.getAttribute("data-target"));
                if (targetInput) {
                    if (targetInput.getAttribute("type") == "password") {
                        targetInput.type = "text";
                        target.classList.add("target-input-type-text");
                    } else {
                        targetInput.type = "password";
                        target.classList.remove("target-input-type-text");
                    }
                }
            }

            // Handle clicks on number step up buttons
            if (target.matches("[data-trigger='number-step-up']") || target.matches("[data-trigger='number-step-up'] *")) {
                if (target.matches("[data-trigger='number-step-up'] *")) {
                    target = target.closest("[data-trigger='number-step-up']");
                }
                var targetInput = document.getElementById(target.getAttribute("data-target"));
                if (targetInput) {
                    if (!document.documentMode) {
                        // Not IE, because document.documentMode is undefined
                        // That property is only available in IE
                        targetInput.stepUp();
                    }
                    else {
                        // In IE, range inputs have the stepUp() and stepDown() functions
                        // Therefore, the following hack implements those functions for number inputs
                        var cloneInput = targetInput.cloneNode(false);
                        cloneInput.setAttribute("type", "range");
                        try {
                            cloneInput.stepUp();
                        }
                        catch (e) { }
                        targetInput.value = cloneInput.value;
                    }
                }
            }

            // Handle clicks on number step down buttons
            if (target.matches("[data-trigger='number-step-down']") || target.matches("[data-trigger='number-step-down'] *")) {
                if (target.matches("[data-trigger='number-step-down'] *")) {
                    target = target.closest("[data-trigger='number-step-down']");
                }
                var targetInput = document.getElementById(target.getAttribute("data-target"));
                if (targetInput) {
                    if (!document.documentMode) {
                        // Not IE, because document.documentMode is undefined
                        // That property is only available in IE
                        targetInput.stepDown();
                    }
                    else {
                        // In IE, range inputs have the stepUp() and stepDown() functions
                        // Therefore, the following hack implements those functions for number inputs
                        var cloneInput = targetInput.cloneNode(false);
                        cloneInput.setAttribute("type", "range");
                        try {
                            cloneInput.stepDown();
                        }
                        catch (e) { }
                        targetInput.value = cloneInput.value;
                    }
                }
            }

            // Call the click handler method to handle any logic set by the user in their projects to handle clicks
            digidive.clickHandler(eventCopy);
        },
        false
    );

    // Adding the key down event listener (for shortcuts and accessibility)
    document.addEventListener(
        "keydown",
        function (event) {
            var eventCopy = event;

            // Shortcuts are triggered only if no input, textarea, or select has focus,
            // If the control key or command key is not pressed down,
            // And if the enabling data attribute is present on the DOM's body
            if (!(document.querySelector("input:focus") || document.querySelector("textarea:focus") || document.querySelector("select:focus"))) {
                event = event || window.event;

                if (!(event.ctrlKey || event.metaKey)) {
                    // Toggle sidebar when [shift] + [S] keys are pressed
                    if (document.body.getAttribute("data-sidebar-shortcut-enabled")) {
                        if (event.shiftKey && event.which == 83) {
                            // Variable to store whether a modal is open or not
                            var modalOpen = false;

                            // Hash exists, so we check if it belongs to a modal
                            if (window.location.hash) {
                                var hash = window.location.hash.substring(1);
                                var elem = document.getElementById(hash);
                                if (elem) {
                                    if (elem.classList.contains("modal")) {
                                        modalOpen = true;
                                    }
                                }
                            }
                            // Check for a modal with the .show class
                            if (document.querySelector(".modal.show")) {
                                modalOpen = true;
                            }

                            // This shortcut works only if no modal is open
                            if (!modalOpen) {
                                digidive.toggleSidebar();
                                event.preventDefault();
                            }
                        }
                    }

                    // Toggle dark mode when [shift] + [D] keys are pressed
                    if (document.body.getAttribute("data-dm-shortcut-enabled")) {
                        if (event.shiftKey && event.which == 68) {
                            digidive.toggleDarkMode();
                            event.preventDefault();
                        }
                    }
                }
            }

            // Handling other keydown events
            if (event.which === 27) {
                // Close dropdown menu (if one is open) when [esc] key is pressed
                if (document.querySelector("[data-toggle='dropdown'].active")) {
                    var elem = document.querySelector("[data-toggle='dropdown'].active");
                    elem.classList.remove("active");
                    elem.closest(".dropdown").classList.remove("show");
                    event.preventDefault();
                }
                // Close modal (if one is open, and if no dropdown menu is open) when [esc] key is pressed
                // Conditional on dropdowns so that dropdowns on modals can be closed with the keyboard without closing the modal
                else {
                    // Hash exists, so we check if it belongs to a modal
                    if (window.location.hash) {
                        var hash = window.location.hash.substring(1);
                        var elem = document.getElementById(hash);
                        if (elem) {
                            if (elem.classList.contains("modal")) {
                                if (!elem.getAttribute("data-esc-dismissal-disabled")) {
                                    window.location.hash = "#";
                                    event.preventDefault();
                                }
                            }
                        }
                    }
                    // Check for a modal with the .show class
                    if (document.querySelector(".modal.show")) {
                        var elem = document.querySelector(".modal.show");
                        if (!elem.getAttribute("data-esc-dismissal-disabled")) {
                            elem.classList.remove("show");
                            event.preventDefault();
                        }
                    }
                }
            }

            // Call the keydown handler method to handle any logic set by the user in their projects to handle keydown events
            digidive.keydownHandler(eventCopy);
        }
    );

    // Handling custom file inputs
    var digidiveCustomFileInputs = document.querySelectorAll(".custom-file input");
    for (var i = 0; i < digidiveCustomFileInputs.length; i++) {
        var customFile = digidiveCustomFileInputs[i];
        // Create file name container element, add the class name, and set default value
        // Append it to the custom file element
        var fileNamesContainer = document.createElement("div");
        fileNamesContainer.classList.add("file-names");
        var dataDefaultValue = customFile.getAttribute("data-default-value");
        if (dataDefaultValue) {
            fileNamesContainer.innerHTML = dataDefaultValue;
        } else {
            fileNamesContainer.innerHTML = "No file chosen";
        }
        customFile.parentNode.appendChild(fileNamesContainer);

        // Add the event listener that will update the contents of the file name container element on change
        customFile.addEventListener(
            "change",
            function (event) {
                fileNamesContainer = event.target.parentNode.querySelector(".file-names");
                if (event.target.files.length === 1) {
                    fileNamesContainer.innerHTML = event.target.files[0].name;
                } else if (event.target.files.length > 1) {
                    fileNamesContainer.innerHTML = event.target.files.length + " files";
                } else {
                    fileNamesContainer.innerHTML = "No file chosen";
                }
            }
        );
    }

    // Setting the initial value on load
    // Adding the event listeners for binding the value
    // Only for elements with the attribute
    // The double event listener is for cross-browser compatibility
    // Mainly, IE does not register the input event, so change must be used
    var digidiveElemsBindValue = document.querySelectorAll("[data-bind-value]");
    for (var i = 0; i < digidiveElemsBindValue.length; i++) {
        digidive.bindInputValue(digidiveElemsBindValue[i]);
        digidiveElemsBindValue[i].addEventListener(
            "input", digidive.callBindInputValueForAttachment, false
        );
        digidiveElemsBindValue[i].addEventListener(
            "change", digidive.callBindInputValueForAttachment, false
        );
    }

    // Adding the .with-transitions class to the page-wrapper so that transitions are enabled
    // This way, the weird bug on Chrome is avoided, where the transitions run on load
    if (digidive.pageWrapper) {
        digidive.pageWrapper.classList.add("with-transitions");
    }
}

var pagenavAdjusted = false

// function adjustPageNav(){
//     // update height of on-this-page-nav
//     var pagenav = document.querySelector('.on-this-page-nav')
//         console.log(pagenav);
//     if (pagenav.length === 0) return;

//     if (pagenavAdjusted === false){
//         var m = 10 
//         var titlebar = document.querySelector('.title-bar')
//         console.log(titlebar.offsetHeight);
//         if (titlebar.length !== 0) m += titlebar.offsetHeight;

//         console.log(m);
//         pagenav.style="height: calc(100vh - var(--navbar-height) - "+m+"px);"
//     }
// }

function inViewport(el) {
    var elH = el.offsetHeight,
        H = window.innerHeight,
        r = el.getBoundingClientRect(),
        t = r.top,
        b = r.bottom;
    return Math.max(0, t > 0 ? Math.min(elH, H - t) : Math.min(b, H));
}

function adjustPageNav() {
    var pagenav = document.querySelector('.on-this-page-nav')
    if (pagenav === null || pagenav.length === 0) {
        function adjustPageNav() { return; }
        return;
    }

    // set height of pagenav
    // var wrapper = document //.querySelector('.content-wrapper')
    var footer = document.querySelector('.page-footer')
    var navbarTopHeight = document.querySelector('.navbar-top').offsetHeight
    var navbarBottomHeight = document.querySelector('.navbar-bottom').offsetHeight
    var navbarHeight = navbarTopHeight - Math.min(navbarTopHeight, window.scrollY) + navbarBottomHeight
    console.log(navbarHeight);
    // var offset = wrapper.scrollTop + wrapper.offsetHeight
    // var offset = window.scrollY
    // var footerVisible = offset - footer.offsetTop
    // footerVisible = Math.max(footerVisible, 0)
    // console.log(offset, footer.offsetTop);

    var footerVisible = inViewport(footer);

    var m = 10 + footerVisible + navbarHeight;
    var style = ""

    var titlebar = document.querySelector('.content-wrapper>.title-bar')
    //
    if (titlebar !== null && titlebar.length !== 0) {
        m += titlebar.offsetHeight;
        style = "top: calc(var(--navbar-height) + " + titlebar.offsetHeight + "px - 1rem); "
    }

    style += "max-height: calc(100vh - " + m + "px);"
    pagenav.style = style
    // set current position

}

// Call the function when the DOM is loaded
document.addEventListener("DOMContentLoaded", digidiveOnDOMContentLoaded);
window.addEventListener("load", adjustPageNav);
document.addEventListener('scroll', adjustPageNav)
