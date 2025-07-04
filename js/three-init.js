import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

class ProductViewer {
    constructor(config) {
        console.log('Initializing ProductViewer with config:', config);
        
        this.container = document.querySelector(config.container);
        if (!this.container) {
            throw new Error('Container not found');
        }
        
        this.modelPath = this.container.dataset.model;
        console.log('Model path from dataset:', this.modelPath);
        
        if (!this.modelPath) {
            throw new Error('Model path not specified');
        }

        this.defaultCameraPosition = config.defaultCameraPosition || [2, 2, 2];
        this.lighting = config.lighting || {
            ambient: 0xffffff,
            intensity: 0.5
        };
        
        this.init();
    }

    init() {
        try {
            console.log('Initializing 3D scene');
            
            // Scene setup
            this.scene = new THREE.Scene();
            this.scene.background = new THREE.Color(0x1a1a1a);

            // Camera setup
            this.camera = new THREE.PerspectiveCamera(
                75,
                this.container.clientWidth / this.container.clientHeight,
                0.1,
                1000
            );
            this.camera.position.set(...this.defaultCameraPosition);

            // Renderer setup
            this.renderer = new THREE.WebGLRenderer({
                antialias: true,
                alpha: true
            });
            this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
            this.renderer.setPixelRatio(window.devicePixelRatio);
            this.renderer.outputColorSpace = THREE.SRGBColorSpace;
            this.container.appendChild(this.renderer.domElement);

            // Lighting
            const ambientLight = new THREE.AmbientLight(
                this.lighting.ambient,
                this.lighting.intensity
            );
            this.scene.add(ambientLight);

            const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
            directionalLight.position.set(5, 5, 5);
            this.scene.add(directionalLight);

            // Controls
            this.controls = new OrbitControls(this.camera, this.renderer.domElement);
            this.controls.enableDamping = true;
            this.controls.dampingFactor = 0.05;
            this.controls.rotateSpeed = 0.5;

            console.log('Scene initialized, loading model...');
            
            // Load model
            this.loadModel();

            // Animation loop
            this.animate();

            // Resize handler
            window.addEventListener('resize', () => this.onWindowResize());
        } catch (error) {
            console.error('Error initializing viewer:', error);
            this.showErrorMessage(error.message);
        }
    }

    loadModel() {
        console.log('Starting model load from path:', this.modelPath);
        
        const loader = new GLTFLoader();
        const loadingOverlay = this.container.querySelector('.loading-overlay');
        
        // Show loading overlay
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }

        // First check if the file exists
        fetch(this.modelPath)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                console.log('Model file found, starting load');
                
                // Now load the model
                loader.load(
                    this.modelPath,
                    (gltf) => {
                        console.log('Model loaded successfully:', gltf);
                        this.model = gltf.scene;
                        
                        // Center the model
                        const box = new THREE.Box3().setFromObject(this.model);
                        const center = box.getCenter(new THREE.Vector3());
                        this.model.position.sub(center);
                        
                        // Scale the model to fit the view
                        const size = box.getSize(new THREE.Vector3());
                        const maxDim = Math.max(size.x, size.y, size.z);
                        const scale = 2 / maxDim;
                        this.model.scale.multiplyScalar(scale);
                        
                        this.scene.add(this.model);
                        console.log('Model added to scene');

                        // Hide loading overlay
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                    },
                    (xhr) => {
                        const percent = (xhr.loaded / xhr.total * 100).toFixed(2);
                        console.log(`Loading progress: ${percent}%`);
                    },
                    (error) => {
                        console.error('Error loading model:', error);
                        this.showErrorMessage('Failed to load 3D model: ' + error.message);
                        // Show 2D image as fallback
                        const heroImage = document.querySelector('.product-hero-image');
                        if (heroImage) {
                            heroImage.style.display = 'block';
                        }
                    }
                );
            })
            .catch(error => {
                console.error('Error fetching model file:', error);
                this.showErrorMessage(`Error fetching model: ${error.message}`);
                // Show 2D image as fallback
                const heroImage = document.querySelector('.product-hero-image');
                if (heroImage) {
                    heroImage.style.display = 'block';
                }
            });
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    onWindowResize() {
        this.camera.aspect = this.container.clientWidth / this.container.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
    }

    showErrorMessage(message) {
        console.error('Viewer error:', message);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'viewer-error';
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <p>${message}</p>
        `;
        this.container.appendChild(errorDiv);

        // Hide loading overlay
        const loadingOverlay = this.container.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'none';
        }
    }
}

// Initialize viewer when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#viewer3d');
    if (container) {
        try {
            console.log('Found viewer container, initializing...');
            const viewer = new ProductViewer({
                container: '#viewer3d',
                defaultCameraPosition: [2, 2, 2],
                lighting: {
                    ambient: 0xffffff,
                    intensity: 0.5
                }
            });
        } catch (error) {
            console.error('Failed to initialize 3D viewer:', error);
            // Show 2D image as fallback
            const heroImage = document.querySelector('.product-hero-image');
            if (heroImage) {
                heroImage.style.display = 'block';
            }
        }
    } else {
        console.log('No viewer container found');
    }
}); 