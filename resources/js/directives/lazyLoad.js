// Lazy load directive
export default {
  mounted(el, binding) {
    const options = {
      root: null,
      rootMargin: '0px',
      threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          // Load the component when it comes into view
          if (binding.value && typeof binding.value === 'function') {
            binding.value();
          }
          observer.unobserve(el);
        }
      });
    }, options);
    
    observer.observe(el);
  }
};