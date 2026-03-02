<template>
  <div class="home">
    <!-- Hero Section -->
    <HeroCarousel />

    <!-- Hero Features -->
    <HeroFeatures />

    <!-- Featured Stories Section -->
    <component :is="FeaturedGalleries" v-if="componentsLoaded.FeaturedGalleries" />

    <!-- Latest News Section -->
    <component :is="LatestNews" v-if="componentsLoaded.LatestNews" />

    <!-- Special Category News Section -->
    <component :is="SpecialCategoryNews" v-if="componentsLoaded.SpecialCategoryNews" />

    <!-- Categories Section -->
    <!-- <component :is="CategoriesSection" v-if="componentsLoaded.CategoriesSection" /> -->

    <!-- OPD Head Greetings Section -->
    <component :is="OpdHeadGreetings" v-if="componentsLoaded.OpdHeadGreetings" />

    <!-- Programs Section -->
    <component :is="ProgramsSection" v-if="componentsLoaded.ProgramsSection" />

    <!-- Upcoming Events -->
    <component :is="EventsSection" v-if="componentsLoaded.EventsSection" />

    <!-- Agenda Section -->
    <component :is="Agenda" v-if="componentsLoaded.Agenda" />

    <!-- Social Media Section -->
    <component :is="SocialMediaSection" v-if="componentsLoaded.SocialMediaSection" />

    <!-- Gallery Section -->
    <component :is="Gallery" v-if="componentsLoaded.Gallery" />

    <!-- Call to Action -->
    <component :is="CtaSection" v-if="componentsLoaded.CtaSection" />
  </div>
</template>

<script>
// Only immediately load critical components above the fold
import HeroCarousel from '../components/HeroCarousel.vue';
import HeroFeatures from '../components/HeroFeatures.vue';
import api from '../services/api';

export default {
  name: 'Home',
  components: {
    // Critical components loaded immediately
    HeroCarousel,
    HeroFeatures
  },
  data() {
    return {
      // Prefetch data for critical sections
      prefetchData: {},
      // Async components
      OpdHeadGreetings: null,
      FeaturedGalleries: null,
      LatestNews: null,
      SpecialCategoryNews: null,
      CategoriesSection: null,
      ProgramsSection: null,
      EventsSection: null,
      Agenda: null,
      SocialMediaSection: null,
      Gallery: null,
      CtaSection: null,
      // Component loading status
      componentsLoaded: {
        OpdHeadGreetings: false,
        FeaturedGalleries: false,
        LatestNews: false,
        SpecialCategoryNews: false,
        CategoriesSection: false,
        ProgramsSection: false,
        EventsSection: false,
        Agenda: false,
        SocialMediaSection: false,
        Gallery: false,
        CtaSection: false
      }
    }
  },
  async beforeMount() {
    // Preload critical data in parallel
    await this.prefetchCriticalData();
    // Load async components
    await this.loadAsyncComponents();
  },
  methods: {
    async prefetchCriticalData() {
      try {
        // Load data that's needed immediately
        const [heroes, news, galleries] = await Promise.all([
          api.getHeroes().catch(() => ({ data: { data: [] } })),
          api.getNews({ per_page: 3 }).catch(() => ({ data: { data: [] } })),
          api.getGalleries({ per_page: 4 }).catch(() => ({ data: { data: [] } }))
        ]);

        this.prefetchData = {
          heroes: heroes.data.data || [],
          news: news.data.data || [],
          galleries: galleries.data.data || []
        };
      } catch (error) {
        console.error('Error prefetching data:', error);
      }
    },
    async loadAsyncComponents() {
      try {
        // Load all async components in parallel
        const [
          FeaturedGalleries,
          LatestNews,
          SpecialCategoryNews,
          CategoriesSection,
          OpdHeadGreetings,
          ProgramsSection,
          EventsSection,
          Agenda,
          SocialMediaSection,
          Gallery,
          CtaSection
        ] = await Promise.all([
          import('../components/FeaturedGalleries.vue'),
          import('../components/LatestNews.vue'),
          import('../components/SpecialCategoryNews.vue'),
          import('../components/CategoriesSection.vue'),
          import('../components/OpdHeadGreetings.vue'),
          import('../components/ProgramsSection.vue'),
          import('../components/EventsSection.vue'),
          import('../components/Agenda.vue'),
          import('../components/SocialMediaSection.vue'),
          import('../components/Gallery.vue'),
          import('../components/CtaSection.vue')
        ]);

        // Assign components
        this.FeaturedGalleries = FeaturedGalleries.default;
        this.LatestNews = LatestNews.default;
        this.SpecialCategoryNews = SpecialCategoryNews.default;
        this.CategoriesSection = CategoriesSection.default;
        this.OpdHeadGreetings = OpdHeadGreetings.default;
        this.ProgramsSection = ProgramsSection.default;
        this.EventsSection = EventsSection.default;
        this.Agenda = Agenda.default;
        this.SocialMediaSection = SocialMediaSection.default;
        this.Gallery = Gallery.default;
        this.CtaSection = CtaSection.default;

        // Update loading status
        Object.keys(this.componentsLoaded).forEach(key => {
          this.componentsLoaded[key] = true;
        });
      } catch (error) {
        console.error('Error loading components:', error);
      }
    }
  }
}
</script>

<style scoped>
* {
  box-sizing: border-box;
}

.home {
  max-width: 100%;
  margin: 0 auto;
  background: #fff;
}
</style>
