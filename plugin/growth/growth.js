(function() {
    'use strict';
    if (typeof GROWTH_CONFIG === 'undefined' || !GROWTH_CONFIG.use) return;

    var C = GROWTH_CONFIG;

    document.addEventListener('DOMContentLoaded', function() {
        // === Item page: balloon above 바로구매 ===
        var buyBtn = document.querySelector('#sit_ov_btn .sit_btn_buy');
        if (buyBtn && !document.querySelector('#sit_ov_btn .growth-balloon')) {
            itemBalloon(buyBtn);
        }

        // === Order page: balloon above 주문하기 ===
        var orderBtn = document.getElementById('btn_submit');
        if (!orderBtn) {
            var candidates = document.querySelectorAll('.btn_confirm button[type="submit"], .btn_confirm input[type="submit"]');
            for (var i = 0; i < candidates.length; i++) {
                var t = candidates[i].value || candidates[i].textContent || '';
                if (t.indexOf('주문하기') !== -1) { orderBtn = candidates[i]; break; }
            }
        }
        if (orderBtn && !orderBtn.parentElement.querySelector('.growth-balloon')) {
            orderBalloon(orderBtn);
        }

        // === Payment methods ===
        var paysel = document.getElementById('sod_frm_paysel');
        if (paysel) {
            if (C.layout_vertical) verticalLayout();
            if (C.top_settle) reorderSettle(paysel);
        }
    });

    function makeBalloon() {
        var d = document.createElement('div');
        d.className = 'growth-balloon' + (C.bounce ? ' growth-bounce' : '');
        d.innerHTML = '<span style="display:inline-block;background:' + C.color +
            ';color:#fff;font-size:12px;font-weight:bold;padding:6px 12px;border-radius:20px;position:relative;">' +
            escapeHtml(C.text) +
            '<span style="position:absolute;bottom:-6px;left:50%;margin-left:-6px;width:0;height:0;' +
            'border-left:6px solid transparent;border-right:6px solid transparent;border-top:6px solid ' +
            C.color + ';"></span></span>';
        return d;
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    function itemBalloon(btn) {
        var w = btn.offsetWidth;
        var h = btn.offsetHeight;
        var cs = window.getComputedStyle(btn);

        var wrap = document.createElement('div');
        wrap.style.cssText = 'float:left;width:' + w + 'px;height:' + h + 'px;' +
            'margin-right:' + cs.marginRight + ';margin-bottom:' + cs.marginBottom + ';position:relative;';

        btn.parentNode.insertBefore(wrap, btn);
        wrap.appendChild(btn);

        btn.style.setProperty('width', '100%', 'important');
        btn.style.setProperty('height', '100%', 'important');
        btn.style.setProperty('float', 'none', 'important');
        btn.style.setProperty('margin', '0', 'important');

        var b = makeBalloon();
        b.style.cssText = 'position:absolute;bottom:100%;left:0;right:0;text-align:center;white-space:nowrap;margin-bottom:4px;z-index:10;';
        wrap.insertBefore(b, btn);
    }

    function orderBalloon(btn) {
        var b = makeBalloon();
        b.style.cssText = 'text-align:center;margin-bottom:8px;';
        btn.parentElement.insertBefore(b, btn);
    }

    function verticalLayout() {
        var paysl = document.getElementById('od_pay_sl');
        if (paysl) paysl.classList.add('growth-vertical-layout');
    }

    function reorderSettle(paysel) {
        var inputs = paysel.querySelectorAll('input[name="od_settle_case"]');
        var target = null, targetLabel = null;

        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].value === C.top_settle) {
                target = inputs[i];
                targetLabel = inputs[i].nextElementSibling;
                break;
            }
        }

        if (!target) {
            for (var i = 0; i < inputs.length; i++) {
                var lbl = inputs[i].nextElementSibling;
                if (lbl && lbl.textContent.trim().indexOf(C.top_settle) !== -1) {
                    target = inputs[i];
                    targetLabel = lbl;
                    break;
                }
            }
        }

        if (!target || !targetLabel) return;

        var legend = paysel.querySelector('legend');
        var ref = legend ? legend.nextSibling : paysel.firstChild;

        paysel.insertBefore(target, ref);
        paysel.insertBefore(targetLabel, target.nextSibling);

        target.checked = true;

        if (C.badge_use && C.badge_text) {
            var badge = document.createElement('span');
            badge.className = 'growth-badge';
            badge.style.background = C.badge_color;
            badge.textContent = C.badge_text;
            targetLabel.style.position = 'relative';
            targetLabel.appendChild(badge);
        }
    }
})();
