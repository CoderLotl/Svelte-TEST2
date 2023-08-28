import { writable } from 'svelte/store';

export const logged = writable(null);
export const user = writable(null);
export const config = writable(null);