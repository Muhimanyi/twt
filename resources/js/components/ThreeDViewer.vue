<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import * as THREE from 'three';
import { OBJLoader } from 'three/examples/jsm/loaders/OBJLoader.js';
import { MTLLoader } from 'three/examples/jsm/loaders/MTLLoader.js';

const props = withDefaults(defineProps<{
    model: string;
    mtl?: string;
    className?: string;
}>(), {
    className: '',
});

const containerRef = ref<HTMLDivElement>();

let scene: THREE.Scene;
let camera: THREE.PerspectiveCamera;
let renderer: THREE.WebGLRenderer;
let group: THREE.Group;
let animationId: number;

const cursorX = ref(0);
const cursorY = ref(0);
const scrollProgress = ref(0);

let time = 0;

onMounted(() => {
    const container = containerRef.value!;

    scene = new THREE.Scene();
    scene.background = null;

    const w = container.clientWidth;
    const h = container.clientHeight;

    camera = new THREE.PerspectiveCamera(45, w / h, 0.1, 1000);
    camera.position.set(0, 0, 4);
    camera.lookAt(0, 0, 0);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.0;
    container.appendChild(renderer.domElement);

    const ambient = new THREE.AmbientLight(0xffffff, 0.5);
    scene.add(ambient);

    const hemi = new THREE.HemisphereLight(0xb3d9ff, 0xffe0b3, 0.6);
    scene.add(hemi);

    const keyLight = new THREE.DirectionalLight(0xffffff, 2.5);
    keyLight.position.set(5, 10, 5);
    scene.add(keyLight);

    const fillLight = new THREE.DirectionalLight(0x8ecae6, 0.4);
    fillLight.position.set(-3, 1, -2);
    scene.add(fillLight);

    const rimLight = new THREE.DirectionalLight(0xfff0d0, 0.5);
    rimLight.position.set(-2, 0, -4);
    scene.add(rimLight);

    group = new THREE.Group();
    scene.add(group);

    function loadObj() {
        const loader = new OBJLoader();
        loader.load(props.model, (obj) => {
            obj.traverse((child) => {
                if (child instanceof THREE.Mesh) {
                    child.material = new THREE.MeshStandardMaterial({
                        color: 0xe8e8e8,
                        roughness: 0.25,
                        metalness: 0.7,
                        envMapIntensity: 1.2,
                    });
                }
            });
            const box = new THREE.Box3().setFromObject(obj);
            const center = box.getCenter(new THREE.Vector3());
            obj.position.sub(center);
            obj.scale.set(0.7, 0.7, 0.7);
            group.add(obj);
        });
    }

    if (props.mtl) {
        const mtlLoader = new MTLLoader();
        mtlLoader.load(props.mtl, (materials) => {
            const objLoader = new OBJLoader();
            objLoader.setMaterials(materials);
            objLoader.load(props.model, (obj) => {
                const box = new THREE.Box3().setFromObject(obj);
                const center = box.getCenter(new THREE.Vector3());
                obj.position.sub(center);
                obj.scale.set(0.05, 0.05, 0.015);
                group.add(obj);
            });
        }, undefined, () => loadObj());
    } else {
        loadObj();
    }

    const handleMouse = (e: MouseEvent) => {
        cursorX.value = (e.clientX / window.innerWidth - 0.5) * 2;
        cursorY.value = (e.clientY / window.innerHeight - 0.5) * -2;
    };

    const handleScroll = () => {
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        scrollProgress.value = docHeight > 0 ? window.scrollY / docHeight : 0;
    };

    window.addEventListener('mousemove', handleMouse);
    window.addEventListener('scroll', handleScroll);
    handleScroll();

    const resize = () => {
        const cw = container.clientWidth;
        const ch = container.clientHeight;
        camera.aspect = cw / ch;
        camera.updateProjectionMatrix();
        renderer.setSize(cw, ch);
    };
    window.addEventListener('resize', resize);

    let currentX = 0;
    let currentY = 0;
    let currentZ = 0;
    let currentPosX = 0;

    const animate = () => {
        animationId = requestAnimationFrame(animate);
        time += 0.01;

        currentX += (cursorX.value * 0.5 - currentX) * 0.03;
        currentY += (cursorY.value * 0.5 - currentY) * 0.03;

        const zigzagTarget = Math.sin(scrollProgress.value * Math.PI * 3) * 1.5;
        currentPosX += (zigzagTarget - currentPosX) * 0.03;

        const rollFromScroll = Math.cos(scrollProgress.value * Math.PI * 3) * 0.15;
        const yawFromScroll = Math.cos(scrollProgress.value * Math.PI * 3) * 0.2;
        const targetBank = -currentX * 0.5 + rollFromScroll;
        const targetPitch = -currentY * 0.4;
        const targetYaw = currentX * 0.5 + yawFromScroll;
        const targetZ = -currentY * 1.5;
        const idleRoll = Math.sin(time * 0.5) * 0.03;
        const idleYaw = Math.sin(time * 0.3) * 0.04;
        const idleBob = Math.sin(time * 1.2) * 0.06;

        group.position.x = currentPosX;
        group.position.y = idleBob;
        group.position.z = targetZ;
        group.rotation.x = targetPitch + idleRoll;
        group.rotation.y = targetYaw + idleYaw;
        group.rotation.z = targetBank;

        renderer.render(scene, camera);
    };
    animate();
});

onUnmounted(() => {
    cancelAnimationFrame(animationId);
    renderer?.dispose();
});
</script>

<template>
    <div ref="containerRef" :class="['w-full h-full', className]"></div>
</template>
