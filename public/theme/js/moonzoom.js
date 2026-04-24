/*!
 * @name        easyzoom
 * @author       <>
 * @modified    Friday, May 15th, 2020
 * @version     2.5.2
 */
// !function(t,e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],function(t){e(t)}):"object"==typeof module&&module.exports?module.exports=t.EasyZoom=e(require("jquery")):t.EasyZoom=e(t.jQuery)}(this,function(o){"use strict";var c,l,d,p,u,f,i={loadingNotice:"Loading image",errorNotice:"The image could not be loaded",errorDuration:2500,linkAttribute:"href",preventClicks:!0,beforeShow:o.noop,beforeHide:o.noop,onShow:o.noop,onHide:o.noop,onMove:o.noop};function s(t,e){this.$target=o(t),this.opts=o.extend({},i,e,this.$target.data()),void 0===this.isOpen&&this._init()}return s.prototype._init=function(){this.$link=this.$target.find("a"),this.$image=this.$target.find("img"),this.$flyout=o('<div class="easyzoom-flyout" />'),this.$notice=o('<div class="easyzoom-notice" />'),this.$target.on({"mousemove.easyzoom touchmove.easyzoom":o.proxy(this._onMove,this),"mouseleave.easyzoom touchend.easyzoom":o.proxy(this._onLeave,this),"mouseenter.easyzoom touchstart.easyzoom":o.proxy(this._onEnter,this)}),this.opts.preventClicks&&this.$target.on("click.easyzoom",function(t){t.preventDefault()})},s.prototype.show=function(t,e){var i=this;if(!1!==this.opts.beforeShow.call(this)){if(!this.isReady)return this._loadImage(this.$link.attr(this.opts.linkAttribute),function(){!i.isMouseOver&&e||i.show(t)});this.$target.append(this.$flyout);var o=this.$target.outerWidth(),s=this.$target.outerHeight(),h=this.$flyout.width(),n=this.$flyout.height(),a=this.$zoom.width(),r=this.$zoom.height();c=Math.ceil(a-h),l=Math.ceil(r-n),c<0&&(c=0),l<0&&(l=0),d=c/o,p=l/s,this.isOpen=!0,this.opts.onShow.call(this),t&&this._move(t)}},s.prototype._onEnter=function(t){var e=t.originalEvent.touches;this.isMouseOver=!0,e&&1!=e.length||(t.preventDefault(),this.show(t,!0))},s.prototype._onMove=function(t){this.isOpen&&(t.preventDefault(),this._move(t))},s.prototype._onLeave=function(){this.isMouseOver=!1,this.isOpen&&this.hide()},s.prototype._onLoad=function(t){t.currentTarget.width&&(this.isReady=!0,this.$notice.detach(),this.$flyout.html(this.$zoom),this.$target.removeClass("is-loading").addClass("is-ready"),t.data.call&&t.data())},s.prototype._onError=function(){var t=this;this.$notice.text(this.opts.errorNotice),this.$target.removeClass("is-loading").addClass("is-error"),this.detachNotice=setTimeout(function(){t.$notice.detach(),t.detachNotice=null},this.opts.errorDuration)},s.prototype._loadImage=function(t,e){var i=new Image;this.$target.addClass("is-loading").append(this.$notice.text(this.opts.loadingNotice)),this.$zoom=o(i).on("error",o.proxy(this._onError,this)).on("load",e,o.proxy(this._onLoad,this)),i.style.position="absolute",i.src=t},s.prototype._move=function(t){if(0===t.type.indexOf("touch")){var e=t.touches||t.originalEvent.touches;u=e[0].pageX,f=e[0].pageY}else u=t.pageX||u,f=t.pageY||f;var i=this.$target.offset(),o=u-i.left,s=f-i.top,h=Math.ceil(o*d),n=Math.ceil(s*p);if(h<0||n<0||c<h||l<n)this.hide();else{var a=-1*n,r=-1*h;this.$zoom.css({top:a,left:r}),this.opts.onMove.call(this,a,r)}},s.prototype.hide=function(){this.isOpen&&!1!==this.opts.beforeHide.call(this)&&(this.$flyout.detach(),this.isOpen=!1,this.opts.onHide.call(this))},s.prototype.swap=function(t,e,i){this.hide(),this.isReady=!1,this.detachNotice&&clearTimeout(this.detachNotice),this.$notice.parent().length&&this.$notice.detach(),this.$target.removeClass("is-loading is-ready is-error"),this.$image.attr({src:t,srcset:o.isArray(i)?i.join():i}),this.$link.attr(this.opts.linkAttribute,e)},s.prototype.teardown=function(){this.hide(),this.$target.off(".easyzoom").removeClass("is-loading is-ready is-error"),this.detachNotice&&clearTimeout(this.detachNotice),delete this.$link,delete this.$zoom,delete this.$image,delete this.$notice,delete this.$flyout,delete this.isOpen,delete this.isReady},o.fn.easyZoom=function(e){return this.each(function(){var t=o.data(this,"easyZoom");t?void 0===t.isOpen&&t._init():o.data(this,"easyZoom",new s(this,e))})},s});
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function($) {
    $.extend($.fn, {
        mooZoom: function(options) {
            var self = this;
                        
            return this.each(function () {
                $(this).parent().css('position', 'relative');                
                $(this).mouseenter(function () {
                    self.onMouseEnter(this, options);
                });
            });
        },
        onMouseEnter: function(el, options) {            
            this._top = $(el).offset().top;
            this._left = $(el).offset().left;
            this._relTop = $(el).position().top;
            this._relLeft = $(el).position().left;
            
            var defaults = {
                zoom: {
                    width: 100,
                    height: 100,
                    zIndex: 600
                },
                overlay: {
                    opacity: 0.65,
                    zIndex: 500,
                    backgroundColor: '#ffffff',
                    fade: false
                },
                detail: {
                    opacity: 1,
                    zIndex: 600,
                    margin: {
                        top: 0,
                        left: 0
                    },                    
                    fade: false 
                },
                animationDuration: 1000
            }
            
            this.options =  $.extend(true, {}, defaults, options);
            
            var ref = $(el).attr('ref');
            if (typeof ref === 'undefined' && ref === false) {
                return;
            }
            
            // read big image
            this.bigImage = new Image();            
            this.bigImage.src = ref;
            
            // read preview image
            this.image = new Image();
            this.image.src = $(el).attr('src');
            
            var self = this;
            this.bigImage.onload = function() {
            
                self.factor = self.bigImage.width / self.image.width;

                $(window).unbind('mousemove');
                $('.mooZoom-overlay').remove();
                $('.mooZoom-detail').remove();
                $('.mooZoom-big-detail').remove();
                $('<div class="mooZoom-overlay"></div>').appendTo('body');
                $('.mooZoom-overlay').css({
                    position: 'absolute',
                    top: self._top + 'px',
                    left: self._left + 'px',
                    width: jQuery(el).width(),
                    height: jQuery(el).height(),
                    backgroundColor: self.options.overlay.backgroundColor,
                    opacity: 0,
                    zIndex: self.options.overlay.zIndex
                });
                if (self.options.overlay.fade) {
                    $('.mooZoom-overlay').animate({opacity: self.options.overlay.opacity}, self.options.animationDuration);
                } else {
                    $('.mooZoom-overlay').css({
                        opacity: self.options.overlay.opacity
                    });
                }

                $(window).mousemove(function(e) {
                    if ($(e.target).hasClass('mooZoom-overlay') 
                        || $(e.target).hasClass('mooZoom-detail')
                        || $(e.target).hasClass('imgZoom')) {
                        self.onMouseMove(e, el);
                    } else {
                        $('.mooZoom-overlay').remove();
                        $('.mooZoom-detail').remove();
                        $('.mooZoom-big-detail').remove();
                        $(window).unbind('mousemove');
                    }             
                });
            }
        },
        onMouseMove: function(e, el) {
            var overlay = $('.mooZoom-detail');            
            var detail = $('.mooZoom-big-detail');
            if ($(overlay).length === 0) {
                $('<div class="mooZoom-detail"></div>').appendTo('body');
            }

            if ($(detail).length === 0) {
                $('<div class="mooZoom-big-detail"></div>').appendTo($(el).parent());
                $('.mooZoom-big-detail').css({
                    position: 'absolute',
                    top: (parseInt(this._relTop) + parseInt(this.options.detail.margin.top)) + 'px',
                    left: (parseInt($(el).outerWidth(true)) + parseInt(this.options.detail.margin.left)) + 'px',
                    width: (this.options.zoom.width * this.factor) + 'px',
                    height: (this.options.zoom.height * this.factor) + 'px',
                    zIndex: this.options.detail.zIndex,
                    opacity: 0,
                    backgroundImage: 'url(' + $(el).attr('ref') + ')'
                });

                if (this.options.detail.fade) {
                    $('.mooZoom-big-detail').animate({opacity: this.options.detail.opacity}, this.options.animationDuration);
                } else {
                    $('.mooZoom-big-detail').css({
                        opacity: this.options.detail.opacity
                    });
                }
            }
            
            var top = this.topPosition(e, el, this.options.zoom.height);
            var left = this.leftPosition(e, el, this.options.zoom.width);
            var pos = '-' + left.bg + 'px -' + top.bg + 'px';
            
            var posBig = '-' + (left.bg * this.factor) + 'px -' + (top.bg * this.factor) + 'px';
            $(detail).css('background-position', posBig);
            $(overlay).css({
                position: 'absolute',
                top: top.top,
                left: left.left,
                width: this.options.zoom.width + 'px',
                height: this.options.zoom.height + 'px',
                zIndex: this.options.zoom.zIndex,
                backgroundImage: 'url(' + $(el).attr('src') + ')'
            });            
            $(overlay).css('background-position', pos);
        },
        topPosition: function(e, el, height) {
            var pos = {top: (e.pageY - (height / 2)), bg: (e.pageY - this._top - (height / 2))};
            if (pos.top <= this._top) {
                pos.top = this._top;
                pos.bg = 0;
            } else if (pos.top >= this._top + ($(el).height() - height)) {
                pos.top = this._top + ($(el).height() - height);
                pos.bg = ($(el).height() - height);
            }
            return pos;
        },
        leftPosition: function(e, el, width) {
            var pos = {left: (e.pageX - (width / 2)), bg: (e.pageX - this._left - (width / 2))};
            if (pos.left <= this._left) {
                pos.left = this._left;
                pos.bg = 0;
            } else if (pos.left >= this._left + ($(el).width() - width)) {
                pos.left = this._left + ($(el).width() - width);
                pos.bg = ($(el).width() - width);
            }
            return pos;
        }
    });
})(jQuery);

var options = {
    zoom: {
        width: 500,
        height: 500,
        zIndex: 600
    },
    overlay: {
        opacity: 0.3,
        zIndex: 500,
        // backgroundColor: '#000000',
        fade: true
    },
    detail: {
        width: 300,
        height: 500,
        zIndex: 600,
        margin: {
            top: 0,
            left: 0
        },
        fade: true
    }
};
jQuery(document).ready(function() {
    jQuery('.imgZoom').mooZoom(options);
});