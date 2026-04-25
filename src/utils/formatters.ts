/**
 * Utility functions for standardized data formatting across the application
 */

/**
 * Format currency values with 2 decimal places and currency symbol
 * @param value - The numeric value to format
 * @param currency - Currency symbol (default: ₱)
 * @returns Formatted currency string
 */
export function formatCurrency(value: number, currency: string = "₱"): string {
  return `${currency}${value.toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;
}

/**
 * Format percentage values with specified decimal places
 * @param value - The numeric value to format (0-100)
 * @param decimals - Number of decimal places (default: 1)
 * @returns Formatted percentage string
 */
export function formatPercentage(value: number, decimals: number = 1): string {
  return `${value.toFixed(decimals)}%`;
}

/**
 * Format file size in human-readable format
 * @param bytes - File size in bytes
 * @returns Formatted file size string
 */
export function formatFileSize(bytes: number): string {
  if (bytes === 0) return "0 Bytes";

  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`;
}

/**
 * Format weight/mass values
 * @param value - The numeric value
 * @param unit - Unit of measurement (default: kg)
 * @returns Formatted weight string
 */
export function formatWeight(value: number, unit: string = "kg"): string {
  return `${value.toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })} ${unit}`;
}

/**
 * Format area values
 * @param value - The numeric value
 * @param unit - Unit of measurement (default: hectares)
 * @returns Formatted area string
 */
export function formatArea(value: number, unit: string = "hectares"): string {
  return `${value.toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })} ${unit}`;
}

/**
 * Validate file size
 * @param file - File object to validate
 * @param maxSizeMB - Maximum file size in MB (default: 2)
 * @returns Object with isValid boolean and error message if invalid
 */
export function validateFileSize(
  file: File,
  maxSizeMB: number = 2
): { isValid: boolean; error?: string } {
  const maxSizeBytes = maxSizeMB * 1024 * 1024;

  if (file.size > maxSizeBytes) {
    return {
      isValid: false,
      error: `File size exceeds maximum of ${maxSizeMB}MB. Current size: ${formatFileSize(file.size)}`,
    };
  }

  return { isValid: true };
}

/**
 * Validate file type for OCR (PDF and images only)
 * @param file - File object to validate
 * @returns Object with isValid boolean and error message if invalid
 */
export function validateOCRFileType(file: File): { isValid: boolean; error?: string } {
  const allowedTypes = [
    "application/pdf",
    "image/jpeg",
    "image/jpg",
    "image/png",
    "image/gif",
  ];

  if (!allowedTypes.includes(file.type)) {
    return {
      isValid: false,
      error: "Only PDF and image files (JPG, PNG, GIF) are supported for OCR",
    };
  }

  return { isValid: true };
}
