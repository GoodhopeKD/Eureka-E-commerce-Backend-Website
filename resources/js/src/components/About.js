import React from "react";
import {colors} from '../config/colors'
import styled from "styled-components";
import Zim from "../Assets/images/zim.jpg";
import des1 from "../Assets/images/des1.png";
import des2 from "../Assets/images/des2.png";
import James from "../Assets/images/des1.png";

const About = () => {
  const teams = [Zim, des1, des2, James];

  const Team = () => teams.map((team) => <Image src={team} />);

  return (
    <Container>
      <Left>
        <LeftContent>
          <Header>About</Header>
          <Discription>
            Lorem ipsum dolor sit amet , consectetur adipiscing elit . Turpis
            est hac fermentum lobortis purus tempor feugiat lectus mattis . Mi ,
            nisi , morbi rutrum tellus nulla rhoncus bibendum eu .
            <br /> <br /> <br />
            Lorem ipsum dolor sit amet , consectetur adipiscing elit . Eu
            sollicitudin rhoncus porttitor pulvinar ac scelerisque ligula odio .
            Aliquam quis consequat donec in ante quis bibendum dictum .
          </Discription>
        </LeftContent>
      </Left>
      <Right>
        <RightContent>
          <Header>Team</Header>
          <ImageContainer> {Team()} </ImageContainer>
        </RightContent>
      </Right>
    </Container>
  );
};

export default About;

const Container = styled.div`
  height: 100vh;
  width: 100%;
  margin-top: 0;
  display: flex;
  justify-content: center;
  align-items: center;

  @media screen and (max-width: 760px) {
    display: block;
  }
`; 

const Right = styled.div`
  height: 100%;
  width: 50%;
  background-color: c ${colors.primary};
  color: white;

  @media screen and (max-width: 760px) {
    width: 100%;
  }
`;

const Left = styled.div`
  height: 100%;
  width: 50%;
  background-color:  ${colors.white};
  color: black;

  @media screen and (max-width: 760px) {
    width: 100%;
  }
`;

const Header = styled.h2`
  font-size: 3.5rem;
  font-weight: bold;
  margin-top: 0;

  @media screen and (max-width: 760px) {
    font-size: 2.5rem;
  }
`;

const LeftContent = styled.div`
  padding: 8rem;

  @media screen and (max-width: 760px) {
    padding: 3rem;
  }

  @media screen and (max-width: 1000px) {
    padding: 5rem;
  }
`;

const RightContent = styled.div`
  padding: 8rem;

  @media screen and (max-width: 760px) {
    padding: 3rem;
  }

  @media screen and (max-width: 1000px) {
    padding: 5rem;
  }

`;

const Discription = styled.span`
  font-size: 1.3rem;

  @media screen and (max-width: 760px) {
    font-size: 1rem;
  }

  @media screen and (max-width: 900px) {
    font-size: 1.2rem;
  }
  
`;

const ImageContainer = styled.div`
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
`;
const Image = styled.img`
  height: 150px;
  width: 150px;
  border-radius: 50%;
  margin-right: 3rem;
  margin-bottom: 2rem;

  

  @media screen and (max-width: 1500px) {
    height: 100px;
  width: 100px;
  margin-right: 2rem;
  margin-bottom: 2rem;
  }
`;
