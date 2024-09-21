document.addEventListener('DOMContentLoaded', function() {
    const banner = document.querySelector('.pro-banner');
    const closeBanner = document.querySelector('.close-banner');
    const filterButtons = document.querySelectorAll('.filter-button');
    const templateCards = document.querySelectorAll('.template-card');

    // Show the banner when the page loads
    banner.style.display = 'flex';

    // Hide the banner when the close button is clicked
    closeBanner.addEventListener('click', function() {
        banner.style.display = 'none';
    });

    // Filtering functionality
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            filterTemplates();
        });
    });

    function filterTemplates() {
        const activeIndustries = getActiveFilters('industry');
        const activeTypes = getActiveFilters('type');
        const activePurposes = getActiveFilters('purpose');

        templateCards.forEach(card => {
            const industryMatch = activeIndustries.length === 0 || activeIndustries.includes(card.dataset.industry);
            const typeMatch = activeTypes.length === 0 || activeTypes.includes(card.dataset.type);
            const purposeMatch = activePurposes.length === 0 || activePurposes.includes(card.dataset.purpose);

            if (industryMatch && typeMatch && purposeMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function getActiveFilters(filterType) {
        return Array.from(document.querySelectorAll(`.filter-button[data-filter="${filterType}"].active`))
            .map(btn => btn.dataset.value);
    }

    // New code for CSV upload and Canva API integration
    const csvFileInput = document.getElementById('csvFileInput');
    if (csvFileInput) {
        csvFileInput.addEventListener('change', handleFileSelect);
    }

    // Add these variables at the top of your script
    const CLIENT_ID = 'YOUR_CLIENT_ID';
    const CLIENT_SECRET = 'YOUR_CLIENT_SECRET';
    const TEMPLATE_ID = 'YOUR_TEMPLATE_ID';

    function handleFileSelect(event) {
        const file = event.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const csv = e.target.result;
            const data = parseCSV(csv);
            processData(data[0]); // We'll just use the first row for this MVP
        };
        
        reader.readAsText(file);
    }

    function parseCSV(csv) {
        const lines = csv.split('\n');
        return lines.map(line => line.split(','));
    }

    async function processData(rowData) {
        try {
            const accessToken = await getAccessToken();
            const autofillJob = await createAutofillJob(accessToken, rowData);
            const designUrl = await getDesignUrl(accessToken, autofillJob.job.id);
            updateUI(designUrl);
        } catch (error) {
            console.error('Error processing data:', error);
        }
    }

    async function getAccessToken() {
        // In a real application, you should implement proper OAuth flow
        // This is a simplified version for demonstration purposes
        const response = await fetch('https://api.canva.com/oauth/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `grant_type=client_credentials&client_id=${CLIENT_ID}&client_secret=${CLIENT_SECRET}`
        });
        const data = await response.json();
        return data.access_token;
    }

    async function createAutofillJob(accessToken, rowData) {
        const response = await fetch('https://api.canva.com/rest/v1/autofills', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                brand_template_id: TEMPLATE_ID,
                data: {
                    // Adjust these fields based on your template and CSV structure
                    COMPANY_NAME: { type: 'text', text: rowData[0] },
                    TAGLINE: { type: 'text', text: rowData[1] },
                    // Add more fields as needed
                }
            })
        });
        return await response.json();
    }

    async function getDesignUrl(accessToken, jobId) {
        let job;
        do {
            const response = await fetch(`https://api.canva.com/rest/v1/autofills/${jobId}`, {
                headers: {
                    'Authorization': `Bearer ${accessToken}`,
                }
            });
            job = await response.json();
            if (job.job.status !== 'success') {
                await new Promise(resolve => setTimeout(resolve, 1000)); // Wait 1 second before checking again
            }
        } while (job.job.status !== 'success');
        
        return job.job.result.design.url;
    }

    function updateUI(designUrl) {
        // Update the first template card with the Canva link
        const firstTemplateCard = document.querySelector('.template-card');
        if (firstTemplateCard) {
            const canvaLink = document.createElement('a');
            canvaLink.href = designUrl;
            canvaLink.textContent = 'Edit in Canva';
            canvaLink.target = '_blank';
            canvaLink.className = 'canva-button';
            
            // Append the Canva link instead of replacing all content
            firstTemplateCard.appendChild(canvaLink);
        }
    }

    // Update the existing code for copying prompt functionality
    const copyButtons = document.querySelectorAll('.copy-prompt-button');

    copyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent any default button behavior
            const prompt = this.getAttribute('data-prompt');
            if (prompt) {
                navigator.clipboard.writeText(prompt).then(() => {
                    // Change button text temporarily to indicate successful copy
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            } else {
                console.error('No prompt available to copy');
                // Optionally, you can show a message to the user here
            }
        });
    });

    const industries = [
        {
            name: 'Startup',
            title: 'Collaborate Smarter',
            description: 'Streamline your team communication and boost productivity with our innovative platform.',
            cta: 'Try for Free',
            color: '#00c4cc',
            image: 'images/communication.jpg'
        },
        {
            name: 'Pizza Shop',
            title: 'Delicious Slices',
            description: 'Handcrafted pizzas made with love, delivered to your door in 30 minutes or less.',
            cta: 'Order Now',
            color: '#d50000',
            image: 'images/pizza.jpg'
        },
        {
            name: 'Charity',
            title: 'Make a Difference',
            description: 'Join us in creating positive change in our community. Every donation counts.',
            cta: 'Donate Today',
            color: '#00c853',
            image: 'images/charity.jpeg'
        }
    ];

    let currentIndustry = 0;

    const mockupAd = document.querySelector('.mockup-ad');
    const changeIndustryButton = document.querySelector('.change-industry-button');

    function updateAd() {
        const industry = industries[currentIndustry];
        mockupAd.innerHTML = `
            <div class="ad-content">
                <h3 class="ad-title">${industry.title}</h3>
                <p class="ad-description">${industry.description}</p>
                <div class="ad-image-placeholder" style="background-image: url('${industry.image}'); background-size: cover; background-position: center;"></div>
                <button class="ad-cta" style="background-color: ${industry.color}">${industry.cta}</button>
            </div>
        `;
    }

    changeIndustryButton.addEventListener('click', function() {
        currentIndustry = (currentIndustry + 1) % industries.length;
        updateAd();
    });

    updateAd(); // Initialize with the first industry
});