<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
  <svg 
    xmlns="http://www.w3.org/2000/svg" 
    viewBox="0 0 340 260" 
    width="340" 
    height="260"
    :class="className"
    v-bind="$attrs"
  >
    <defs>
      <radialGradient id="bgGrad" cx="50%" cy="50%" r="70%">
        <stop offset="0%" stop-color="#07162c"/>
        <stop offset="100%" stop-color="#020610"/>
      </radialGradient>

      <filter id="glowCyan" x="-30%" y="-30%" width="160%" height="160%">
        <feGaussianBlur stdDeviation="5" result="blur" />
        <feMerge>
          <feMergeNode in="blur" />
          <feMergeNode in="SourceGraphic" />
        </feMerge>
      </filter>
      <filter id="glowBlue" x="-40%" y="-40%" width="180%" height="180%">
        <feGaussianBlur stdDeviation="7" result="blur" />
        <feMerge>
          <feMergeNode in="blur" />
          <feMergeNode in="SourceGraphic" />
        </feMerge>
      </filter>

      <linearGradient id="shieldBody" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#01b4e4"/>
        <stop offset="40%" stop-color="#0052b4"/>
        <stop offset="100%" stop-color="#001840"/>
      </linearGradient>
      
      <linearGradient id="shieldBorder" x1="0%" y1="0%" x2="0%" y2="100%">
        <stop offset="0%" stop-color="#3bf0ff"/>
        <stop offset="100%" stop-color="#004899"/>
      </linearGradient>

      <linearGradient id="cyanGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#3ef8ff"/>
        <stop offset="100%" stop-color="#008fa8"/>
      </linearGradient>

      <linearGradient id="greenGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#3bf75e"/>
        <stop offset="100%" stop-color="#108c27"/>
      </linearGradient>

      <linearGradient id="orangeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#ff9922"/>
        <stop offset="100%" stop-color="#b83f00"/>
      </linearGradient>
    </defs>

    <rect width="100%" height="100%" fill="url(#bgGrad)"/>

    <g stroke="#00d8ff" stroke-width="0.75" opacity="0.35">
      <line x1="110" y1="95" x2="135" y2="55"/>
      <line x1="135" y1="55" x2="175" y2="35"/>
      <line x1="175" y1="35" x2="215" y2="60"/>
      <line x1="175" y1="35" x2="180" y2="85"/>
      <line x1="135" y1="55" x2="180" y2="85"/>
      <line x1="110" y1="95" x2="180" y2="85"/>
      <line x1="110" y1="95" x2="140" y2="130"/>
      <line x1="140" y1="130" x2="180" y2="85"/>
      <line x1="180" y1="85" x2="215" y2="60"/>
      <line x1="215" y1="60" x2="235" y2="95"/>
      <line x1="180" y1="85" x2="235" y2="95"/>
      <line x1="110" y1="95" x2="88" y2="132"/>
      <line x1="140" y1="130" x2="88" y2="132"/>
      <line x1="135" y1="55" x2="160" y2="60" stroke-width="0.4"/>
      <line x1="175" y1="35" x2="160" y2="60" stroke-width="0.4"/>
      <line x1="180" y1="85" x2="160" y2="60" stroke-width="0.4"/>
      <line x1="180" y1="85" x2="205" y2="110" stroke-width="0.4"/>
      <line x1="235" y1="95" x2="205" y2="110" stroke-width="0.4"/>
    </g>

    <g filter="url(#glowCyan)">
      <circle cx="175" cy="35" r="5" fill="#ffffff"/>
      <circle cx="175" cy="35" r="3" fill="#00f0ff"/>
      <circle cx="135" cy="55" r="4" fill="#00d8ff"/>
      <circle cx="215" cy="60" r="4" fill="#00d8ff"/>
      <circle cx="110" cy="95" r="4.5" fill="#00d8ff"/>
      <circle cx="235" cy="95" r="3.5" fill="#00d8ff"/>
      <circle cx="140" cy="130" r="3.5" fill="#00d8ff"/>
      <circle cx="88" cy="132" r="4" fill="#00d8ff"/>
      <rect x="156" y="56" width="6" height="6" fill="#a5f3fc" transform="rotate(45 159 59)"/>
      <rect x="202" y="107" width="5" height="5" fill="#a5f3fc" transform="rotate(15 204.5 109.5)"/>
    </g>

    <g stroke="url(#orangeGrad)" stroke-width="6.5" fill="none" stroke-linecap="round" filter="url(#glowBlue)">
      <path d="M 215,50 C 240,45 270,70 265,88" />
      <path d="M 270,100 C 288,122 288,148 270,170" />
    </g>

    <g stroke="url(#greenGrad)" stroke-width="6.5" fill="none" stroke-linecap="round">
      <path d="M 260,180 C 248,202 222,222 195,222" />
      <path d="M 178,231 C 152,248 126,231 108,213" />
      <path d="M 140,205 L 105,188" />
    </g>

    <g stroke="#0066f5" stroke-width="5.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path d="M 220,38 L 246,60 L 228,86" />
      <path d="M 105,188 L 78,171 L 78,127" />
      <path d="M 127,218 L 157,248 L 191,231" />
    </g>

    <g filter="url(#glowBlue)">
      <path d="M 122,100 L 91,117 L 91,173 L 140,156" fill="none" stroke="#00f3ff" stroke-width="9" stroke-linecap="square" stroke-linejoin="miter"/>
      <path d="M 122,100 L 91,117 L 91,173 L 140,156" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="square" stroke-linejoin="miter"/>
    </g>

    <g filter="url(#glowBlue)">
      <path d="M 262,100 C 284,117 284,151 262,172" fill="none" stroke="url(#orangeGrad)" stroke-width="8.5" stroke-linecap="round"/>
      <path d="M 259,103 C 278,118 278,150 259,169" fill="none" stroke="#ffffff" stroke-width="2" opacity="0.6"/>
    </g>

    <g transform="translate(0, -2)">
      <path d="M 170,72 C 200,72 218,81 218,111 C 218,155 170,195 170,200 C 170,195 122,155 122,111 C 122,81 140,72 170,72 Z" 
            fill="url(#shieldBody)" filter="url(#glowBlue)"/>
      
      <path d="M 170,77 C 194,77 210,85 210,111 C 210,147 170,185 170,189 C 170,185 130,147 130,111 C 130,85 146,77 170,77 Z" 
            fill="none" stroke="url(#shieldBorder)" stroke-width="3.5"/>

      <path d="M 170,77 C 146,77 130,85 130,111 C 130,147 170,189 170,189 Z" 
            fill="#000000" opacity="0.32"/>

      <line x1="170" y1="77" x2="170" y2="189" stroke="#56f9ff" stroke-width="1" opacity="0.6"/>

      <g filter="url(#glowCyan)">
        <path d="M 147,122 L 165,142 L 209,95" fill="none" stroke="#00f3ff" stroke-width="10.5" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M 147,122 L 165,142 L 209,95" fill="none" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
      </g>

      <g transform="translate(162, 148)">
        <path d="M 3.5,7 L 3.5,3.5 C 3.5,1.5 5.5,0 8,0 C 10.5,0 12.5,1.5 12.5,3.5 L 12.5,7" fill="none" stroke="#00f3ff" stroke-width="2"/>
        <rect x="0" y="6" width="16" height="12" rx="2.5" fill="url(#cyanGrad)" stroke="#002244" stroke-width="0.75"/>
        <circle cx="8" cy="10" r="1.5" fill="#001835"/>
        <polygon points="7,11 9,11 9.5,15 6.5,15" fill="#001835"/>
      </g>
    </g>

    <circle cx="170" cy="120" r="50" fill="#00f3ff" opacity="0.04" filter="url(#glowBlue)"/>
  </svg>
</template>