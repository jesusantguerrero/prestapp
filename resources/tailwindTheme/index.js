const colors = require("tailwindcss/colors");
const customColors = require('./colors');

module.exports = {
    defaultDark: {
        primary: colors.pink[500],
        "secondary": "#95b3f9",
        "accent": "#7c5bbf",
        "neutral": "#232130",
        "base-deep-1": customColors.rhino[800],
        "base-100": customColors.rhino[900],
        "base-lvl-1": customColors.rhino[800],
        "base-lvl-2": customColors.rhino[700],
        "base-lvl-3": customColors.rhino[600],
        info: "#3D68F5",
        success: "#79E7AE",
        warning: "#D39E17",
        error: "#F61909",
        "body": "white",
        "body-1": colors.slate[200]
    },
    defaultLight: {
        primary: "#F37EA1",
        secondary: "#7B77D1",
        "accent": "#f782c2",
        "neutral": "#F3F4F6",
        "base-deep-1": colors.slate[400],
        "base-100": "#F3F4F6",
        "base-lvl-1": colors.slate[100],
        "base-lvl-2": colors.slate[50],
        "base-lvl-3": colors.white,
        info: "#3D68F5",
        success: "#00C875",
        warning: "#D39E17",
        error: "#F61909",
        "body": colors.gray[900],
        "body-1": colors.gray[700]
    },
    pinkLight: {
        primary: "#d527b7", // "#3a4a73"
        secondary: "#8a00d4",
        "accent": "#f782c2",
        "neutral": "#F3F4F6",
        "base-deep-1": colors.slate[400],
        "base-100": "#F3F4F6",
        "base-lvl-1": colors.slate[100],
        "base-lvl-2": colors.slate[50],
        "base-lvl-3": colors.white,
        info: "#3D68F5",
        success: "#00C875",
        warning: "#D39E17",
        error: "#F61909",
        "body": colors.gray[900],
        "body-1": colors.gray[700]
    },
    blue: {
        primary: colors.blue[400],
        "secondary": "#162f4d",
        "accent": "#a3cdff",
        "neutral": "#d1e6ff",
        "base-deep-1": colors.slate[400],
        "base-100": "#F3F4F6",
        "base-lvl-1": colors.slate[100],
        "base-lvl-2": colors.slate[50],
        "base-lvl-3": colors.white,
        info: "#3D68F5",
        success: "#79E7AE",
        warning: "#D39E17",
        error: "#F61909",
        "body": "#2E384D",
        "body-1": "#9298AD"
    },
    jemm: {
        primary: "#5F0404",
        "primary-dark": "#290202",
        "primary-light": "#D40909",
        "primary-shade-1": "#FFB4A1",
        "primary-shade-2": "#EF8777",
        "primary-shade-3": "#E27C6C",
        "primary-shade-4": "#380000",
        "secondary": "#00203D",
        "accent": "#005B5C",
        "highlight": "#F65D4E",
        "neutral": "#FFF5F1", // "#BEA6A1" #FCEEEE
        "base-deep-1": colors.slate[400],
        "base-100": "#F5F6FA",
        "base-lvl-1": colors.slate[100],
        "base-lvl-2": colors.slate[50],
        "base-lvl-3": colors.white,
        info: "#00E3DC",
        success: "#00C9C4",
        warning: "#D2B48C",
        error: "#F65D4E",
        "body": "#2E384D",
        "body-1": "#9298AD"
    },
    realEstate: {
        // Main brand colors
        primary: "#1E40AF", // Deep blue - professional and trustworthy
        "primary-light": "#3B82F6", // Lighter blue for hover states
        "primary-shade-1": "#2563EB", // Mid-tone blue
        "primary-shade-2": "#1D4ED8", // Slightly darker blue
        "primary-shade-3": "#1E40AF", // Deep blue
        "primary-shade-4": "#1E3A8A", // Darkest blue
        
        // Secondary colors
        "secondary": "#059669", // Emerald - success and growth
        "accent": "#0EA5E9", // Sky blue - fresh and modern
        "highlight": "#6366F1", // Indigo - for important elements
        
        // Neutral colors
        "neutral": "#64748B", // Slate - professional gray
        "base-deep-1": colors.slate[600],
        "base-100": "#F8FAFC", // Very light slate
        "base-lvl-1": colors.slate[100],
        "base-lvl-2": colors.slate[50],
        "base-lvl-3": colors.white,
        
        // Status colors
        info: "#3B82F6", // Blue
        success: "#10B981", // Emerald
        warning: "#F59E0B", // Amber
        error: "#EF4444", // Red
        
        // Text colors
        "body": "#0F172A", // Slate 900 - main text
        "body-1": "#475569" // Slate 600 - secondary text
    },
    blueLight: {
        primary: "#47A9F1",
        "primary-light": "#63D0DD",
        "primary-shade-1": "#47A9F1",
        "primary-shade-2": "#47A9F1",
        "primary-shade-3": "#47A9F1",
        "primary-shade-4": "#47A9F1",
        // "secondary": "#7069DE",
        "secondary": "#0C165B",
        "accent": "#5F47DD",
        "highlight": "#00E3DC",
        "neutral": "#ECEEF5",
        "base-deep-1": colors.slate[400],
        "base-100": "#F5F6FA",
        "base-lvl-1": colors.slate[100],
        "base-lvl-2": colors.slate[50],
        "base-lvl-3": colors.white,
        info: "#3D68F5",
        success: "#79E7AE",
        warning: "#D39E17",
        error: "#F61909",
        "body": "#2E384D",
        "body-1": "#9298AD"
    }
}
