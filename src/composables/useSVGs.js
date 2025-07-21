/**
 * useSVGs composable
 * Returns the SVG animation sequence for the hero section.
 */
import ItsAnim from '../assets/stop-blending-in/its.svg';
import TimeAnim from '../assets/stop-blending-in/time.svg';
import ToAnim from '../assets/stop-blending-in/to.svg';
import StandAnim from '../assets/stop-blending-in/stand.svg';
import OutAnim from '../assets/stop-blending-in/out.svg';

export function useSVGs() {
  const svgs = [
    { component: ItsAnim, color: 'fill-id-purple' },
    { component: TimeAnim, color: 'fill-id-purple' },
    { component: ToAnim, color: 'fill-id-purple' },
    { component: StandAnim, color: 'fill-id-purple' },
    { component: OutAnim, color: 'fill-id-purple' },
  ];
  return { svgs };
}
