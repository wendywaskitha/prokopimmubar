<template>
  <div class="gallery-test-page">
    <h1>Gallery Detail Page Test</h1>
    <p>This is a test page to verify the Gallery Detail component works correctly.</p>
    <router-link to="/gallery">Back to Gallery</router-link>
  </div>
</template>

<script>
export default {
  name: 'GalleryTest'
}
</script>

<style scoped>
.gallery-test-page {
  padding: 2rem;
  text-align: center;
}
</style>