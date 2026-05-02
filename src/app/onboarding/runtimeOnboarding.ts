const seenOnboardingKeys = new Set<string>();

export function hasSeenOnboarding(key: string) {
  return seenOnboardingKeys.has(key);
}

export function markOnboardingSeen(key: string) {
  seenOnboardingKeys.add(key);
}
