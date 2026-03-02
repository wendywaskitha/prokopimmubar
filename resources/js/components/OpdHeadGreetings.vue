<template>
    <section class="opd-head-greetings-section py-5">
        <div class="container">
            <div class="section-header mb-5">
                <h2 class="section-title text-center mb-2">Kolom Kepala OPD</h2>
                <p class="section-subtitle text-center text-muted mb-4">
                    Ucapan kepala OPD Kabupaten Muna Barat
                </p>
                <div class="title-divider"></div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-5">
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p class="loading-text mt-3">Memuat data...</p>
                </div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="text-center py-5">
                <div class="error-container">
                    <h4 class="error-title">Terjadi Kesalahan</h4>
                    <p class="error-message mb-4">{{ error }}</p>
                    <button class="btn btn-retry" @click="fetchBanners">
                        Coba Lagi
                    </button>
                </div>
            </div>

            <!-- Slider -->
            <div v-else-if="banners.length > 0" class="greetings-slider">
                <div class="slider-container">
                    <div
                        class="slider-wrapper"
                        :style="{
                            transform: `translateX(-${currentIndex * 100}%)`,
                        }"
                    >
                        <div
                            v-for="(slideGroup, groupIndex) in groupedBanners"
                            :key="groupIndex"
                            class="slider-item"
                        >
                            <div class="slide-group">
                                <article
                                    v-for="banner in slideGroup"
                                    :key="banner.id"
                                    class="greeting-card"
                                    @mouseenter="stopAutoSlide"
                                    @mouseleave="startAutoSlide"
                                    @click="handleBannerClick(banner)"
                                >
                                    <div class="card-image-container">
                                        <img
                                            :src="banner.image_url"
                                            class="card-img"
                                            :alt="banner.title"
                                            @error="handleImageError"
                                            loading="lazy"
                                        />
                                        <div class="card-overlay">
                                            <span class="overlay-text"
                                                >Lihat Detail</span
                                            >
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <h3 class="card-title">
                                            {{ banner.title }}
                                        </h3>
                                        <time class="card-date">{{
                                            formatDate(banner.created_at)
                                        }}</time>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <button
                        class="slider-nav prev"
                        @click="prevSlide"
                        :disabled="currentIndex === 0"
                        aria-label="Previous slide"
                    >
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M15 18L9 12L15 6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>
                    <button
                        class="slider-nav next"
                        @click="nextSlide"
                        :disabled="currentIndex >= groupedBanners.length - 1"
                        aria-label="Next slide"
                    >
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <path
                                d="M9 18L15 12L9 6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </button>

                    <!-- Indicators -->
                    <div class="slider-indicators">
                        <span
                            v-for="n in groupedBanners.length"
                            :key="n"
                            class="indicator"
                            :class="{ active: n - 1 === currentIndex }"
                            @click="goToSlide(n - 1)"
                        ></span>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-5">
                <div class="empty-state">
                    <h4 class="empty-title">Belum Ada Data</h4>
                    <p class="empty-message">
                        Belum ada ucapan dari kepala OPD saat ini.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import BannerService from "../services/BannerService";

export default {
    name: "OpdHeadGreetings",
    data() {
        return {
            banners: [],
            loading: true,
            error: null,
            currentIndex: 0,
            autoSlideInterval: null,
            itemsPerSlide: 4,
        };
    },
    computed: {
        groupedBanners() {
            const groups = [];
            for (let i = 0; i < this.banners.length; i += this.itemsPerSlide) {
                groups.push(this.banners.slice(i, i + this.itemsPerSlide));
            }
            return groups;
        },
    },
    async mounted() {
        await this.fetchBanners();
        this.startAutoSlide();
        this.updateItemsPerSlide();
        window.addEventListener("resize", this.updateItemsPerSlide);
    },
    beforeUnmount() {
        this.stopAutoSlide();
        window.removeEventListener("resize", this.updateItemsPerSlide);
    },
    methods: {
        async fetchBanners() {
            try {
                this.loading = true;
                this.error = null;
                const response = await BannerService.getOpdHeadGreetings();
                this.banners = response.data.data;
                this.preloadImages();
            } catch (error) {
                console.error("Error fetching OPD head greetings:", error);
                this.error = "Gagal memuat data. Silakan coba lagi nanti.";
            } finally {
                this.loading = false;
            }
        },
        preloadImages() {
            this.banners.forEach((banner) => {
                const img = new Image();
                img.src = banner.image_url;
            });
        },
        handleBannerClick(banner) {
            BannerService.trackClick(banner.id).catch((error) => {
                console.error("Error tracking banner click:", error);
            });

            if (banner.link) {
                window.open(banner.link, "_blank");
            }
        },
        handleImageError(event) {
            event.target.src = "/images/placeholder-banner.png";
            event.target.classList.add("image-error");
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString("id-ID", {
                day: "numeric",
                month: "short",
                year: "numeric",
            });
        },
        updateItemsPerSlide() {
            if (window.innerWidth >= 1200) {
                this.itemsPerSlide = 4;
            } else if (window.innerWidth >= 992) {
                this.itemsPerSlide = 3;
            } else if (window.innerWidth >= 768) {
                this.itemsPerSlide = 2;
            } else {
                this.itemsPerSlide = 1;
            }
        },
        nextSlide() {
            if (this.currentIndex < this.groupedBanners.length - 1) {
                this.currentIndex++;
            }
        },
        prevSlide() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
            }
        },
        goToSlide(index) {
            this.currentIndex = Math.max(
                0,
                Math.min(index, this.groupedBanners.length - 1)
            );
        },
        startAutoSlide() {
            this.stopAutoSlide(); // Hentikan interval sebelumnya jika ada

            if (this.groupedBanners.length > 1) {
                this.autoSlideInterval = setInterval(() => {
                    if (this.currentIndex < this.groupedBanners.length - 1) {
                        this.currentIndex++;
                    } else {
                        this.currentIndex = 0;
                    }
                }, 6000);
            }
        },
        stopAutoSlide() {
            if (this.autoSlideInterval) {
                clearInterval(this.autoSlideInterval);
                this.autoSlideInterval = null;
            }
        },
    },
};
</script>

<style scoped>
/* Modern CSS Variables */
:root {
    --primary: #2563eb;
    --primary-hover: #1d4ed8;
    --secondary: #64748b;
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-muted: #94a3b8;
    --background: #ffffff;
    --surface: #b9b9b9;
    --border: #e2e8f0;
    --border-light: #f1f5f9;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    --radius-sm: 6px;
    --radius: 8px;
    --radius-lg: 12px;
    --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Base Section */
.opd-head-greetings-section {
    padding: 3rem 0;
    background: #f5f5f5; /* Abu-abu terang */
    min-height: 400px;
}

/* Header */
.section-header {
    max-width: 600px;
    margin: 0 auto;
}

.section-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.025em;
    margin-bottom: 0.5rem;
}

.section-subtitle {
    font-size: 1rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.title-divider {
    width: 40px;
    height: 3px;
    background: var(--primary);
    margin: 0 auto;
    border-radius: 2px;
}

/* Loading State */
.loading-container {
    padding: 4rem 0;
}

.loading-spinner {
    width: 32px;
    height: 32px;
    border: 3px solid var(--border);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

.loading-text {
    font-size: 0.875rem;
    color: var(--text-muted);
    font-weight: 500;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Error State */
.error-container {
    background: var(--background);
    padding: 3rem 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    max-width: 400px;
    margin: 0 auto;
    border: 1px solid var(--border);
}

.error-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.error-message {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

.btn-retry {
    background: var(--primary);
    color: white;
    border: none;
    padding: 0.625rem 1.5rem;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
}

.btn-retry:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Empty State */
.empty-state {
    background: var(--background);
    padding: 3rem 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    max-width: 400px;
    margin: 0 auto;
    border: 1px solid var(--border-light);
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.empty-message {
    font-size: 0.875rem;
    color: var(--text-secondary);
    line-height: 1.5;
}

/* Slider */
.greetings-slider {
    position: relative;
    padding: 2rem 0;
}

.slider-container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    overflow: hidden;
    padding: 0 60px;
}

.slider-wrapper {
    display: flex;
    transition: transform 0.4s ease-out;
    width: 100%;
}

.slider-item {
    min-width: 100%;
    padding: 0 0.75rem;
}

.slide-group {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    justify-content: center;
    align-items: stretch;
}

.greeting-card {
    background: var(--background);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
    transition: var(--transition-slow);
    cursor: pointer;
    display: flex;
    flex-direction: column;
    min-width: 0;
    height: 100%;
}

/* Card Design */
.greeting-card {
    background: var(--background);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-light);
    transition: var(--transition-slow);
    cursor: pointer;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.greeting-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
    border-color: var(--border);
}

.card-image-container {
    position: relative;
    height: 350px;
    background: var(--surface);
    overflow: hidden;
}

.card-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: var(--transition-slow);
}

.greeting-card:hover .card-img {
    transform: scale(1.05);
}

.card-img.image-error {
    object-fit: contain;
    background: var(--border-light);
}

.card-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.greeting-card:hover .card-overlay {
    opacity: 1;
}

.overlay-text {
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--radius);
    backdrop-filter: blur(4px);
}

.card-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    text-align: center; /* Posisi teks di tengah */
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex-grow: 1;
    text-align: center; /* Posisi title di tengah */
}

.card-date {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 500;
    background: var(--surface);
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-sm);
    align-self: center; /* Posisi date di tengah */
    text-align: center;
}

/* Navigation */
.slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: #28a745; /* Hijau */
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    z-index: 10;
    box-shadow: var(--shadow-md);
}

.slider-nav:hover {
    background: #218838; /* Hijau lebih gelap saat hover */
    box-shadow: var(--shadow-lg);
}

.slider-nav:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #6c757d; /* Abu-abu saat disabled */
}

.slider-nav:disabled:hover {
    background: #6c757d;
}

.slider-nav svg {
    color: white; /* Warna putih untuk ikon */
}

.slider-nav.prev {
    left: 10px;
}

.slider-nav.next {
    right: 10px;
}

/* Indicators */
.slider-indicators {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
}

.indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--border);
    cursor: pointer;
    transition: var(--transition);
}

.indicator:hover,
.indicator.active {
    background: var(--primary);
}

.indicator.active {
    transform: scale(1.25);
}

/* Responsive Design */
@media (max-width: 1199px) {
    .slide-group {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 991px) {
    .slider-container {
        padding: 0 50px;
    }

    .slide-group {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .card-image-container {
        height: 180px;
    }
}

@media (max-width: 767px) {
    .section-title {
        font-size: 1.5rem;
    }

    .section-subtitle {
        font-size: 0.875rem;
    }

    .slider-container {
        padding: 0 40px;
    }

    .slide-group {
        gap: 0.75rem;
    }

    .card-content {
        padding: 1rem;
    }

    .card-title {
        font-size: 0.875rem;
    }

    .card-image-container {
        height: 160px;
    }

    .slider-nav {
        width: 36px;
        height: 36px;
    }

    .slider-nav.prev {
        left: 5px;
    }

    .slider-nav.next {
        right: 5px;
    }
}

@media (max-width: 575px) {
    .slider-container {
        padding: 0 30px;
    }

    .slide-group {
        grid-template-columns: 1fr;
    }

    .card-image-container {
        height: 300px;
    }

    .greetings-slider {
        padding: 1rem 0;
    }

    .loading-container,
    .error-container,
    .empty-state {
        padding: 2rem 1rem;
    }

    .section-header {
        padding: 0 1rem;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    .greeting-card,
    .slider-nav,
    .indicator,
    .card-img,
    .card-overlay,
    .slider-wrapper,
    .btn-retry {
        transition: none;
    }

    .loading-spinner {
        animation: none;
    }
}

.slider-nav:focus,
.indicator:focus,
.greeting-card:focus,
.btn-retry:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* Print Styles */
@media print {
    .slider-nav,
    .slider-indicators {
        display: none;
    }

    .greeting-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid var(--border);
    }

    .slider-item {
        min-width: 50%;
    }
}
</style>
