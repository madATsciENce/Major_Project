function showClimateContent(season) {
    const climateContent = document.getElementById("climate-content");
  
    // Define content for each season
    const seasonContent = {
      summer: `
        <h3>Summer</h3>
        <p>
          The Summer season in India, from April to mid-June, brings scorching temperatures, 
          especially in the northern plains. This is the hottest period of the year.
        </p>
      `,
      monsoon: `
        <h3>Monsoon</h3>
        <p>
          The Monsoon season, from mid-June to September, brings heavy rainfall that is crucial for agriculture 
          and replenishing water resources across the country.
        </p>
      `,
      winter: `
        <h3>Winter</h3>
        <p>
          The Winter season in India spans from November to February. It varies across regions, with the northern 
          parts experiencing snowfall and colder temperatures.
        </p>
      `
    };
  
    // Update the content box based on the selected season
    climateContent.innerHTML = seasonContent[season] || "<p>Select a season to see the details.</p>";
  }
  