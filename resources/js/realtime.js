/**
 * AgriTech Pro — Real-Time WebSocket Client
 *
 * Uses Laravel Reverb + Echo to listen for broadcast events
 * and update the UI instantly without page refreshes.
 */
(function() {
    'use strict';

    // ── Configuration ──────────────────────────────────────────────────
    const CONFIG = {
        userId: window.Laravel?.userId || document.querySelector('meta[name="user-id"]')?.content,
        isAdmin: (window.Laravel?.isAdmin || document.querySelector('meta[name="is-admin"]')?.content) === 'true',
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
    };

    // ── Toast System ───────────────────────────────────────────────────
    function showToast(message, type = 'info', duration = 6000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle',
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="toast-icon ${icons[type] || icons.info}"></i>
            <p class="toast-msg">${message}</p>
            <button class="toast-dismiss" onclick="this.parentElement.classList.add('toast-dismissing');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>
        `;

        container.appendChild(toast);
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.add('toast-visible');
            });
        });

        setTimeout(() => {
            toast.classList.add('toast-dismissing');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    window.showToast = showToast;

    // ── Notification Badge Updater ──────────────────────────────────────
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notifBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.style.display = '';
            } else {
                badge.style.display = 'none';
                badge.textContent = '0';
            }
        }

        document.querySelectorAll('.notif-unread-count').forEach(el => {
            el.textContent = count;
            el.style.display = count > 0 ? '' : 'none';
        });

        const baseTitle = document.title.replace(/^\(\d+\+?\)\s*/, '');
        document.title = count > 0 ? `(${count}) ${baseTitle}` : baseTitle;
    }

    function incrementNotificationBadge() {
        const badge = document.getElementById('notifBadge');
        const current = (badge && badge.style.display !== 'none') ? (parseInt(badge.textContent) || 0) : 0;
        updateNotificationBadge(current + 1);
    }

    // ── Live Data Updaters ─────────────────────────────────────────────

    function prependToList(containerId, html) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const empty = container.querySelector('.empty-state, .no-data');
        if (empty && container.children.length <= 1) empty.remove();

        const temp = document.createElement('div');
        temp.innerHTML = html;
        const newItem = temp.firstElementChild;
        if (newItem) {
            container.prepend(newItem);
            newItem.style.opacity = '0';
            newItem.style.transform = 'translateY(-10px)';
            requestAnimationFrame(() => {
                newItem.style.transition = 'all 0.3s ease';
                newItem.style.opacity = '1';
                newItem.style.transform = 'translateY(0)';
            });
        }
    }

    function animateCounter(elementId, newValue, prefix = '', suffix = '') {
        const el = document.getElementById(elementId);
        if (!el) return;

        const current = parseInt(el.textContent.replace(/[^0-9.-]/g, '')) || 0;
        if (current === newValue) return;

        const duration = 800;
        const start = performance.now();

        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = Math.round(current + (newValue - current) * eased);
            el.textContent = prefix + value.toLocaleString() + suffix;
            if (progress < 1) requestAnimationFrame(update);
        }

        requestAnimationFrame(update);
    }

    // ── Notification Dropdown Helper ───────────────────────────────────
    function addNotificationToDropdown(data) {
        const list = document.getElementById('notifList');
        if (!list) return;

        const item = document.createElement('a');
        item.href = data.action_url || '#';
        item.style.cssText = 'display:flex;gap:12px;padding:12px 18px;text-decoration:none;border-bottom:1px solid var(--border);background:var(--green-50);transition:background .15s;animation:fadeInUp 0.3s ease;';
        item.innerHTML = `
            <div style="width:36px;height:36px;border-radius:50%;background:var(--green-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="${data.icon || 'fas fa-bell'}" style="color:${data.icon_color || 'var(--primary)'};font-size:.85rem;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.8125rem;font-weight:700;color:var(--text);margin-bottom:2px;">${data.title}</div>
                <div style="font-size:.75rem;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${data.message}</div>
                <div style="font-size:.75rem;color:var(--text-muted);margin-top:3px;">Just now</div>
            </div>
            <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:4px;"></div>
        `;

        list.prepend(item);
        while (list.children.length > 8) {
            list.lastChild.remove();
        }
    }

    // ── Connection Status ──────────────────────────────────────────────
    function showConnectionStatus(status) {
        let indicator = document.getElementById('ws-status');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'ws-status';
            indicator.style.cssText = 'position:fixed;bottom:8px;left:8px;z-index:99998;padding:4px 10px;border-radius:20px;font-size:.7rem;font-weight:600;transition:all 0.3s ease;';
            document.body.appendChild(indicator);
        }

        if (status === 'connected') {
            indicator.style.background = '#dcfce7';
            indicator.style.color = '#16a34a';
            indicator.innerHTML = '<i class="fas fa-circle" style="font-size:.5rem;margin-right:4px;"></i>Live';
            setTimeout(() => { indicator.style.opacity = '0'; }, 3000);
        } else if (status === 'disconnected') {
            indicator.style.background = '#fef2f2';
            indicator.style.color = '#dc2626';
            indicator.innerHTML = '<i class="fas fa-circle" style="font-size:.5rem;margin-right:4px;"></i>Reconnecting...';
            indicator.style.opacity = '1';
        }
    }

    // ── Initialize Echo Listeners ──────────────────────────────────────
    function initEcho() {
        if (!CONFIG.userId) {
            console.log('[RealTime] No user ID — real-time disabled');
            return;
        }

        if (typeof Echo === 'undefined') {
            console.log('[RealTime] Laravel Echo not loaded — retrying...');
            setTimeout(initEcho, 500);
            return;
        }

        console.log('[RealTime] Initializing WebSocket listeners...');

        // ── User private channel ──────────────────────────────────
        const userChannel = Echo.private(`user.${CONFIG.userId}`);

        userChannel.listen('.notification.created', (data) => {
            incrementNotificationBadge();
            showToast(`${data.title}`, 'info', 8000);
            addNotificationToDropdown(data);

            // Live update if on notifications page
            const notifPage = document.getElementById('notifications-list');
            if (notifPage) {
                const item = document.createElement('div');
                item.className = 'notification-item unread';
                item.style.cssText = 'animation:fadeInUp .3s ease;border-bottom:1px solid var(--border);padding:16px 20px;display:flex;gap:14px;align-items:flex-start;background:var(--green-50);';
                item.innerHTML = `
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--green-100);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="${data.icon || 'fas fa-bell'}" style="color:${data.icon_color || 'var(--primary)'};font-size:.9rem;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:.875rem;font-weight:700;color:var(--text);margin-bottom:3px;">${data.title}</div>
                        <div style="font-size:.8125rem;color:var(--text-muted);line-height:1.4;">${data.message}</div>
                        <div style="font-size:.75rem;color:var(--text-muted);margin-top:4px;">Just now</div>
                    </div>
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);flex-shrink:0;margin-top:6px;"></div>
                `;
                notifPage.prepend(item);
            }
        });

        userChannel.listen('.order.placed', (data) => {
            showToast(`New order: ${data.order_number} — MWK ${parseInt(data.total).toLocaleString()}`, 'success');

            // Add new order row to admin orders table
            const ordersTable = document.querySelector('#tab-orders .data-table tbody');
            if (ordersTable) {
                const emptyRow = ordersTable.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();

                const sc = {'pending':'badge-gray','confirmed':'badge-sky','packing':'badge-earth','dispatched':'badge-sky','on_the_way':'badge-purple','delivered':'badge-green','cancelled':'badge-coral','refunded':'badge-gray'};
                const badgeClass = sc[data.status] || 'badge-gray';
                const date = new Date(data.created_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});

                const row = document.createElement('tr');
                row.style.cssText = 'animation:fadeInUp .3s ease;background:var(--green-50);';
                row.setAttribute('data-order-id', data.id);
                row.innerHTML = `
                    <td style="font-family:var(--font-mono);font-size:.78rem;font-weight:700;color:var(--primary);">${data.order_number}</td>
                    <td style="font-size:.8rem;">${data.buyer_name}</td>
                    <td style="font-size:.8rem;">—</td>
                    <td style="font-weight:700;">${data.currency || 'MWK'} ${parseInt(data.total).toLocaleString()}</td>
                    <td><span class="badge badge-gray" style="font-size:.67rem;">Pending</span></td>
                    <td><span class="badge ${badgeClass}" style="font-size:.67rem;">${data.status}</span></td>
                    <td style="font-size:.72rem;"><span style="color:var(--text-muted);font-size:.68rem;">—</span></td>
                    <td style="font-size:.72rem;"><span style="color:var(--text-muted);font-size:.68rem;">—</span></td>
                    <td style="font-size:.78rem;color:var(--text-muted);">${date}</td>
                    <td><span style="font-size:.72rem;color:var(--text-muted);">Awaiting action</span></td>
                `;
                ordersTable.prepend(row);
                incrementNotificationBadge();
            }
        });

        userChannel.listen('.order.status_changed', (data) => {
            const statusLabel = data.new_status.replace(/_/g, ' ');
            showToast(`Order ${data.order_number}: ${statusLabel}`, 'info');

            const sc = {'pending':'badge-gray','confirmed':'badge-sky','packing':'badge-earth','dispatched':'badge-sky','on_the_way':'badge-purple','delivered':'badge-green','cancelled':'badge-coral','refunded':'badge-gray'};
            const badgeClass = sc[data.new_status] || 'badge-gray';

            // Update admin orders table status badge
            const adminTable = document.querySelector('#tab-orders .data-table tbody');
            if (adminTable) {
                const rows = adminTable.querySelectorAll('tr[data-order-id]');
                rows.forEach(row => {
                    if (row.getAttribute('data-order-id') == data.id) {
                        const statusCell = row.querySelectorAll('td')[5];
                        if (statusCell) {
                            statusCell.innerHTML = `<span class="badge ${badgeClass}" style="font-size:.67rem;">${statusLabel}</span>`;
                            statusCell.style.animation = 'none';
                            statusCell.offsetHeight;
                            statusCell.style.animation = 'fadeInUp .3s ease';
                        }
                        row.style.background = 'var(--blue-50)';
                        setTimeout(() => { row.style.background = ''; }, 2000);
                    }
                });
            }

            // Update customer my-orders page status badge + progress bar
            const orderCards = document.querySelectorAll('.order-card[data-order-number]');
            orderCards.forEach(card => {
                if (card.getAttribute('data-order-number') === data.order_number) {
                    // Update status badge
                    const statusBadge = card.querySelector('.badge.' + badgeClass.replace('badge-', 'badge-')) || card.querySelector('.badge');
                    const allBadges = card.querySelectorAll('.badge');
                    allBadges.forEach(b => {
                        if (b.textContent.trim() === data.old_status.replace(/_/g, ' ') || b.textContent.trim() === data.new_status.replace(/_/g, ' ')) {
                            b.className = `badge ${badgeClass}`;
                            b.textContent = statusLabel;
                        }
                    });

                    // Update progress bar dots
                    const steps = ['pending','confirmed','packing','dispatched','on_the_way','delivered'];
                    const newIdx = steps.indexOf(data.new_status);
                    if (newIdx >= 0) {
                        const dots = card.querySelectorAll('.status-dot');
                        const labels = card.querySelectorAll('.status-lbl');
                        dots.forEach((dot, i) => {
                            dot.classList.remove('done', 'active');
                            if (i < newIdx) dot.classList.add('done');
                            if (i === newIdx) dot.classList.add('active');
                        });
                        labels.forEach((lbl, i) => {
                            lbl.classList.remove('done', 'active');
                            if (i < newIdx) lbl.classList.add('done');
                            if (i === newIdx) lbl.classList.add('active');
                        });
                    }

                    // Flash highlight
                    card.style.border = '2px solid var(--primary)';
                    card.style.boxShadow = '0 0 0 4px rgba(22,163,74,.15)';
                    setTimeout(() => {
                        card.style.border = '';
                        card.style.boxShadow = '';
                    }, 3000);
                }
            });
        });

        userChannel.listen('.delivery.status_updated', (data) => {
            showToast(`Delivery ${data.tracking_number}: ${data.new_status.replace(/_/g, ' ')}`, 'info');
            if (typeof window.updateTrackingMap === 'function') {
                window.updateTrackingMap(data);
            }
        });

        userChannel.listen('.driver.location_updated', (data) => {
            if (typeof window.updateDriverMarker === 'function') {
                window.updateDriverMarker(data.lat, data.lng);
            }
            const distEl = document.getElementById('distance-remaining');
            if (distEl && data.distance_remaining_km !== undefined) {
                distEl.textContent = `${data.distance_remaining_km} km remaining`;
            }
        });

        userChannel.listen('.course.enrolled', (data) => {
            showToast(`Enrolled in: ${data.course_title}`, 'success');
        });

        userChannel.listen('.course.completed', (data) => {
            showToast(`Congratulations! You completed "${data.course_title}"!`, 'success', 10000);
        });

        userChannel.listen('.lesson.completed', (data) => {
            showToast(`Lesson complete: ${data.lesson_title} (${data.course_progress}%)`, 'success');

            // Update progress bar + counts if on course-detail page
            const pctEl = document.getElementById('progress-pct');
            const barEl = document.getElementById('progress-bar');
            const countEl = document.getElementById('progress-count');

            if (pctEl) pctEl.textContent = data.course_progress + '%';
            if (barEl) barEl.style.width = data.course_progress + '%';
            if (countEl) {
                const match = countEl.textContent.match(/\/(\d+)/);
                const total = match ? match[1] : '?';
                const current = countEl.textContent.match(/^(\d+)/);
                const newCount = current ? parseInt(current[1]) + 1 : '?';
                countEl.textContent = newCount + '/' + total + ' lessons completed';
            }

            // Mark lesson row as done on the page
            const lessonRows = document.querySelectorAll('.lesson-row');
            lessonRows.forEach(row => {
                const btn = row.querySelector(`[onclick*="openLessonModal(${data.lesson_id}"]`);
                if (btn) {
                    const numEl = row.querySelector('.lesson-num');
                    if (numEl && !numEl.classList.contains('done')) {
                        numEl.classList.add('done');
                        numEl.innerHTML = '<i class="fas fa-check" style="font-size:.75rem;"></i>';
                    }
                }
            });

            // If course completed, replace progress section
            if (data.course_completed) {
                const section = document.getElementById('progress-section');
                if (section) {
                    section.outerHTML = '<div id="progress-section" style="background:var(--green-50);border:1.5px solid var(--green-200);border-radius:var(--radius-lg);padding:16px 22px;margin-bottom:22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;"><span class="body-base font-700" style="color:var(--green-700);">🎉 Course Completed!</span></div>';
                }
            }
        });

        userChannel.listen('.farm.alert_created', (data) => {
            const icon = data.priority === 'urgent' ? '🚨' : '⚠️';
            showToast(`${icon} ${data.title}`, data.priority === 'urgent' ? 'error' : 'warning', 10000);
        });

        userChannel.listen('.product.approved', (data) => {
            showToast(`Your product "${data.name}" has been approved!`, 'success');
        });

        // ── Admin channels ────────────────────────────────────────
        if (CONFIG.isAdmin) {
            const adminChannel = Echo.private('admin.dashboard');

            adminChannel.listen('.user.registered', (data) => {
                incrementNotificationBadge();
                showToast(`New farmer: ${data.full_name}`, 'info');
                const el = document.getElementById('stat-farmers');
                if (el) {
                    const cur = parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
                    animateCounter('stat-farmers', cur + 1);
                }
                prependToList('admin-activity-feed',
                    `<tr style="animation:fadeInUp .3s ease">
                        <td>${new Date().toLocaleString()}</td>
                        <td>New farmer registered</td>
                        <td>${data.full_name}</td>
                        <td><span class="badge badge-green">success</span></td>
                    </tr>`
                );
            });

            adminChannel.listen('.product.created', (data) => {
                incrementNotificationBadge();
                showToast(`New listing: ${data.name}`, 'info');
                const el = document.getElementById('stat-products');
                if (el) {
                    const cur = parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
                    animateCounter('stat-products', cur + 1);
                }
            });

            adminChannel.listen('.order.placed', (data) => {
                incrementNotificationBadge();
                showToast(`New order: ${data.order_number}`, 'warning');
                const el = document.getElementById('stat-orders');
                if (el) {
                    const cur = parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
                    animateCounter('stat-orders', cur + 1);
                }

                // Add new order row to admin orders table
                const ordersTable = document.querySelector('#tab-orders .data-table tbody');
                if (ordersTable) {
                    const emptyRow = ordersTable.querySelector('td[colspan]');
                    if (emptyRow) emptyRow.closest('tr').remove();

                    const sc = {'pending':'badge-gray','confirmed':'badge-sky','packing':'badge-earth','dispatched':'badge-sky','on_the_way':'badge-purple','delivered':'badge-green','cancelled':'badge-coral','refunded':'badge-gray'};
                    const badgeClass = sc[data.status] || 'badge-sky';
                    const date = new Date(data.created_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});

                    const row = document.createElement('tr');
                    row.style.cssText = 'animation:fadeInUp .3s ease;background:var(--green-50);';
                    row.setAttribute('data-order-id', data.id);
                    row.innerHTML = `
                        <td style="font-family:var(--font-mono);font-size:.78rem;font-weight:700;color:var(--primary);">${data.order_number}</td>
                        <td style="font-size:.8rem;">${data.buyer_name}</td>
                        <td style="font-size:.8rem;">—</td>
                        <td style="font-weight:700;">${data.currency || 'MWK'} ${parseInt(data.total).toLocaleString()}</td>
                        <td><span class="badge badge-gray" style="font-size:.67rem;">Pending</span></td>
                        <td><span class="badge ${badgeClass}" style="font-size:.67rem;">${data.status}</span></td>
                        <td style="font-size:.72rem;"><span style="color:var(--text-muted);font-size:.68rem;">—</span></td>
                        <td style="font-size:.72rem;"><span style="color:var(--text-muted);font-size:.68rem;">—</span></td>
                        <td style="font-size:.78rem;color:var(--text-muted);">${date}</td>
                        <td><span style="font-size:.72rem;color:var(--text-muted);">Awaiting action</span></td>
                    `;
                    ordersTable.prepend(row);
                }
            });

            adminChannel.listen('.order.status_changed', (data) => {
                incrementNotificationBadge();
                const statusLabel = data.new_status.replace(/_/g, ' ');
                showToast(`Order ${data.order_number}: ${statusLabel}`, 'info');
                const el = document.getElementById('stat-orders');
                if (el) {
                    const cur = parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
                    animateCounter('stat-orders', cur + 1);
                }

                // Update status badge in admin orders table
                const sc = {'pending':'badge-gray','confirmed':'badge-sky','packing':'badge-earth','dispatched':'badge-sky','on_the_way':'badge-purple','delivered':'badge-green','cancelled':'badge-coral','refunded':'badge-gray'};
                const badgeClass = sc[data.new_status] || 'badge-gray';
                const adminTable = document.querySelector('#tab-orders .data-table tbody');
                if (adminTable) {
                    const rows = adminTable.querySelectorAll('tr[data-order-id]');
                    rows.forEach(row => {
                        if (row.getAttribute('data-order-id') == data.id) {
                            const statusCell = row.querySelectorAll('td')[5];
                            if (statusCell) {
                                statusCell.innerHTML = `<span class="badge ${badgeClass}" style="font-size:.67rem;">${statusLabel}</span>`;
                                statusCell.style.animation = 'none';
                                statusCell.offsetHeight;
                                statusCell.style.animation = 'fadeInUp .3s ease';
                            }
                            row.style.background = 'var(--blue-50)';
                            setTimeout(() => { row.style.background = ''; }, 2000);
                        }
                    });
                }
            });

            adminChannel.listen('.delivery.status_updated', (data) => {
                showToast(`Delivery ${data.tracking_number}: ${data.new_status.replace(/_/g, ' ')}`, 'info');
            });

            adminChannel.listen('.disease.alert_created', (data) => {
                incrementNotificationBadge();
                showToast(`Disease Alert: ${data.title}`, 'error', 10000);
            });

            Echo.private('admin.farm-alerts').listen('.farm.alert_created', (data) => {
                incrementNotificationBadge();
                showToast(`Farm Alert: ${data.title}`, data.priority === 'urgent' ? 'error' : 'warning');
            });
        }

        // ── Marketplace (all users) ───────────────────────────────
        Echo.private('marketplace').listen('.product.created', (data) => {
            const grid = document.getElementById('product-grid');
            if (grid) {
                const empty = grid.querySelector('.empty-state, .no-data');
                if (empty) empty.remove();
            }
        });

        // ── Innovation hub ────────────────────────────────────────
        Echo.private('innovation.hub').listen('.innovation.vote_updated', (data) => {
            const voteEl = document.querySelector(`[data-innovation-votes="${data.id}"]`);
            if (voteEl) {
                voteEl.textContent = data.vote_count;
                voteEl.style.transform = 'scale(1.3)';
                setTimeout(() => { voteEl.style.transform = 'scale(1)'; }, 300);
            }
        });

        // ── Disease alerts ────────────────────────────────────────
        Echo.private('disease.alerts').listen('.disease.alert_created', (data) => {
            showToast(`⚠️ Disease Alert: ${data.title}`, 'warning', 10000);
        });

        console.log('[RealTime] All listeners registered');
        showConnectionStatus('connected');

        // Monitor connection state
        if (Echo.connector && Echo.connector.socket) {
            Echo.connector.socket.on('connect', () => showConnectionStatus('connected'));
            Echo.connector.socket.on('disconnect', () => showConnectionStatus('disconnected'));
        }
    }

    // ── Global Functions ───────────────────────────────────────────────
    window.markNotifRead = function(id, el) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CONFIG.csrfToken,
                'Accept': 'application/json'
            }
        });

        if (el) {
            el.style.background = 'transparent';
            const dot = el.querySelector('[style*="border-radius:50%"][style*="primary"]');
            if (dot) dot.remove();
        }

        const badge = document.getElementById('notifBadge');
        if (badge && badge.style.display !== 'none') {
            const current = parseInt(badge.textContent) || 1;
            updateNotificationBadge(Math.max(0, current - 1));
        }
    };

    window.markAllRead = function() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CONFIG.csrfToken }
        }).then(() => {
            document.querySelectorAll('#notifList a').forEach(a => {
                a.style.background = 'transparent';
            });
            updateNotificationBadge(0);
            showToast('All notifications marked as read', 'success');
        });
    };

    // ── Init ───────────────────────────────────────────────────────────
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initEcho);
    } else {
        initEcho();
    }

})();
