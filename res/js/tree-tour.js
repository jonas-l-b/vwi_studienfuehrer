// Instance the tour
var tour = new Tour({
  steps: [
  {
    element: "#welcome",
    title: "Title of my step",
	placement: "auto",
    content: "Content of my step"
  },
  {
    element: "#optionen",
    title: "Title of my step",
	placement: "bottom",
    content: "Content of my step"
  }
],
  storage: false});

// Initialize the tour
tour.init();

// Start the tour
tour.start();