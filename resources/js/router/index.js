import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import News from '../views/News.vue'
import NewsDetail from '../views/NewsDetail.vue'
import NewsCategories from '../views/NewsCategories.vue'
import SearchResults from '../views/SearchResults.vue'
import Gallery from '../views/Gallery.vue'
import GalleryDetail from '../views/GalleryDetail.vue'
import SocialMedia from '../views/SocialMedia.vue'
import Complaint from '../views/Complaint.vue'
import ComplaintTracking from '../views/ComplaintTracking.vue'
import Programs from '../views/Programs.vue'
import ProgramDetail from '../views/ProgramDetail.vue'
import Agenda from '../views/Agenda.vue'
import AgendaDetail from '../views/AgendaDetail.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/news',
    name: 'News',
    component: News
  },
  {
    path: '/news/categories',
    name: 'NewsCategories',
    component: NewsCategories
  },
  {
    path: '/news/:slug',
    name: 'NewsDetail',
    component: NewsDetail,
    props: true
  },
  {
    path: '/search',
    name: 'SearchResults',
    component: SearchResults,
    props: (route) => ({ query: route.query.q })
  },
  {
    path: '/gallery',
    name: 'Gallery',
    component: Gallery
  },
  {
    path: '/gallery/:id',
    name: 'GalleryDetail',
    component: GalleryDetail,
    props: true
  },
  {
    path: '/social-media',
    name: 'SocialMedia',
    component: SocialMedia
  },
  {
    path: '/complaint',
    name: 'Complaint',
    component: Complaint
  },
  {
    path: '/complaint/tracking',
    name: 'ComplaintTracking',
    component: ComplaintTracking
  },
  {
    path: '/programs',
    name: 'Programs',
    component: Programs
  },
  {
    path: '/programs/:id',
    name: 'ProgramDetail',
    component: ProgramDetail,
    props: true
  },
  {
    path: '/agenda',
    name: 'Agenda',
    component: Agenda
  },
  {
    path: '/agenda/:id',
    name: 'AgendaDetail',
    component: AgendaDetail,
    props: true
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  // Scroll behavior
  scrollBehavior(to, from, savedPosition) {
    // If there's a saved position (e.g., back button), use it
    if (savedPosition) {
      return savedPosition
    }
    
    // If the route has a hash (e.g., #section), scroll to that element
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth'
      }
    }
    
    // Otherwise, scroll to the top
    return { top: 0 }
  }
})

export default router