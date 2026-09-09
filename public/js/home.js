/* ============================================================
   HOME PAGE JAVASCRIPT
   ============================================================ */
"use strict";

/* ---- COURSE FILTER ---- */
(function initCourseFilter() {
    const filterBtns = document.querySelectorAll("#coursesFilter .filter-btn");
    const cards = document.querySelectorAll("#coursesGrid .course-card");

    filterBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            filterBtns.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            const filter = btn.dataset.filter;
            cards.forEach((card) => {
                const cat = card.dataset.category || "all";
                const show = filter === "all" || cat === filter;
                card.style.display = show ? "" : "none";
                if (show) {
                    card.style.animation = "fadeInUp 0.4s ease both";
                    setTimeout(() => (card.style.animation = ""), 500);
                }
            });
        });
    });
})();

/* ---- NAV SEARCH ---- */
(function initNavSearch() {
    const input = document.getElementById("navSearchInput");
    if (!input) return;
    input.addEventListener("keypress", (e) => {
        if (e.key === "Enter" && input.value.trim()) {
            window.showToast(
                `🔍 Searching for "${input.value.trim()}"...`,
                "info",
            );
            input.value = "";
        }
    });
})();